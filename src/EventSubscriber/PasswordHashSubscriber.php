<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordHashSubscriber
{
    public function __construct(protected UserPasswordHasherInterface $encoder)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            $this->encode($entity);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            $this->encode($entity);

            $meta = $args->getEntityManager()->getClassMetadata(\get_class($entity));
            $args->getEntityManager()->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
        }
    }

    private function encode(User $user): void
    {
        if ($user->getPlainPassword() !== null) {
            $encoded = $this->encoder->hashPassword($user, $user->getPlainPassword());

            $user->setPassword($encoded);
        }
    }
}
