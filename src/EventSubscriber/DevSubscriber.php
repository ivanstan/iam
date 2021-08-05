<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class DevSubscriber implements EventSubscriberInterface
{
    private string $env;

    private static array $messages = ['info', 'warning', 'danger', 'success'];

    public function __construct($env)
    {
        $this->env = $env;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!\in_array($this->env, ['dev', 'test'])) {
            return;
        }

        foreach (self::$messages as $type) {
            if ($event->getRequest()->query->get($type) !== null) {
                $flashBag = $event->getRequest()->getSession()->getFlashBag();
                $flashBag->add($type, 'This is a sample message. It\'s only available in dev and test environments.');
            }
        }
    }
}
