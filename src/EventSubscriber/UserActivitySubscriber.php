<?php

namespace App\EventSubscriber;

use App\Repository\SessionRepository;
use App\Service\DateTimeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserActivitySubscriber implements EventSubscriberInterface
{
    public function __construct(protected EntityManagerInterface $em, protected SessionRepository $repository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ];
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        $sessionId = $event->getRequest()->getSession()->getId();
        $session = $this->repository->findOneBy(['sessionId' => $sessionId]);

        if ($session !== null) {
            $session->setLastAccess(DateTimeService::getCurrentUTC());
            $this->em->flush();
        }
    }
}
