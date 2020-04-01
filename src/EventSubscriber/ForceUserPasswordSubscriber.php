<?php

namespace App\EventSubscriber;

use App\Controller\ProfileController;
use App\Entity\User;
use App\Service\Traits\TranslatorAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ForceUserPasswordSubscriber implements EventSubscriberInterface
{
    use TranslatorAwareTrait;

    protected const REDIRECT_TO = [ProfileController::class, 'account'];

    protected TokenStorageInterface $tokenStorage;
    protected UrlGeneratorInterface $urlGenerator;

    public function __construct(TokenStorageInterface $tokenStorage, UrlGeneratorInterface $urlGenerator)
    {
        $this->tokenStorage = $tokenStorage;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    protected function condition(): bool
    {
        if (!$this->tokenStorage->getToken()) {
            return false;
        }

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        return !(!is_object($user) || $user->getPassword() !== null);
    }

    protected function controller(Request $request): Response
    {
        $request->getSession()->getFlashBag()->add(
            'info',
            $this->translator->trans('settings.password_is_null')
        );

        return new RedirectResponse(
            $this->urlGenerator->generate('user_profile_security')
        );
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if ($this->condition() && $this->getController($event) !== self::REDIRECT_TO) {
            $event->setController(fn() => $this->controller($event->getRequest()));
        }
    }

    protected function getController(ControllerEvent $event): array
    {
        $controller = $event->getController();

        if (is_array($controller) && isset($controller[0])) {
            $controller[0] = get_class($controller[0]);
        }

        return $controller;
    }
}
