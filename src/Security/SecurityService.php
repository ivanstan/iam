<?php

namespace App\Security;

use App\Entity\Token\UserRecoveryToken;
use App\Entity\Token\UserToken;
use App\Entity\Token\UserVerificationToken;
use App\Entity\User;
use App\Service\DateTimeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityService
{
    private EntityManagerInterface $em;
    private RequestStack $requestStack;
    private TokenStorageInterface $tokenStorage;
    private EventDispatcherInterface $eventDispatcher;
    private SessionInterface $session;

    public function __construct(
        EntityManagerInterface $em,
        RequestStack $requestStack,
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher,
        SessionInterface $session
    ) {
        $this->requestStack = $requestStack;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
        $this->session = $session;
        $this->em = $em;
    }

    public function login(User $user): void
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
        $request = $this->requestStack->getMasterRequest();

        if ($request && !$request->hasPreviousSession()) {
            $request->setSession($this->session);
            $session = $request->getSession();

            if ($session) {
                $session->start();
                $request->cookies->set($session->getName(), $session->getId());
            }
        }

        $this->tokenStorage->setToken($token);
        $this->session->set('_security_common', serialize($token));

        $event = new InteractiveLoginEvent($this->requestStack->getMasterRequest(), $token);
        $this->eventDispatcher->dispatch($event);
    }

    public function logout(): void
    {
        $this->tokenStorage->setToken();
        $this->session->invalidate();
    }

    public function verify(string $token): ?User
    {
        return $this->useToken(
            $this->em->getRepository(UserToken::class)->getToken($token, UserVerificationToken::class)
        );
    }

    public function recover(string $token): ?User
    {
        return $this->useToken(
            $this->em->getRepository(UserToken::class)->getToken($token, UserRecoveryToken::class)
        );
    }

    /**
     * Use token to login. Set user verified and dispose of used token.
     *
     * @param UserToken|null $token
     *
     * @return User|null
     * @throws \Exception
     */
    private function useToken(?UserToken $token): ?User
    {
        if (!$token || !$token->isValid(DateTimeService::getCurrentUTC())) {
            return null;
            // ToDo throw exception here
        }

        $user = $token->getUser();

        $this->login($user);

        $user->setVerified(true);
        $user->setUpdated();

        $this->em->remove($token);
        $this->em->flush();

        return $user;
    }
}
