<?php

namespace App\EventSubscriber;

use App\Entity\Lock;
use App\Entity\User;
use App\Service\Traits\LoggerAwareTrait;
use App\Service\Traits\TranslatorAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;

class SecuritySubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    use TranslatorAwareTrait;

    public const LOGIN_ATTEMPTS_BAN = 5;
    public const LOGIN_ATTEMPTS_MESSAGE = 3;

    private EntityManagerInterface $em;
    private RequestStack $request;

    public function __construct(
        EntityManagerInterface $em,
        RequestStack $request
    ) {
        $this->em = $em;
        $this->request = $request;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $lock = $this->em->getRepository(Lock::class)->getLock(
            AuthenticationEvents::AUTHENTICATION_FAILURE,
            $event->getRequest()->getClientIp()
        );

        if ($lock && $lock->getValue() > self::LOGIN_ATTEMPTS_BAN) {
            throw new AccessDeniedHttpException(
                $this->translator->trans(
                    'You have been denied access. If you believe this was a mistake, please contact administrator.',
                    [
                        '%ip%' => $event->getRequest()->getClientIp(),
                    ]
                )
            );
        }
    }

    public function onAuthenticationSuccess(AuthenticationEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof User) {
            return;
        }

        $this->logger->warning(sprintf('Open session for user %s', $user->getEmail()));

        if ($user && !$user->isActive()) {
            $user->setActive(true);
            $this->em->flush();

            $session = $this->request->getCurrentRequest()->getSession();
            $session->getFlashBag()->add(
                'success',
                $this->translator->trans('Welcome back, your account is active again.')
            );
            // ToDo: delete any pending account delete requests.
        }
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        if (!$this->request->getCurrentRequest()) {
            return;
        }

        $lock = $this->em
            ->getRepository(Lock::class)
            ->updateLock(
                AuthenticationEvents::AUTHENTICATION_FAILURE,
                $this->request->getCurrentRequest()->getClientIp()
            );

        if ($lock !== null) {
            $attemptsLeft = self::LOGIN_ATTEMPTS_BAN - $lock->getValue();

            if ($attemptsLeft <= self::LOGIN_ATTEMPTS_MESSAGE) {
                /** @var Session $session */
                $session = $this->request->getCurrentRequest()->getSession();

                $session->getFlashBag()->add(
                    'danger',
                    $this->translator->trans(
                        'You have %attempts_left% attempts left. You risk being denied access.',
                        ['%attempts_left%' => $attemptsLeft]
                    )
                );
            }
        }
    }
}
