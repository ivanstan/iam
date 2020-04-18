<?php

namespace App\Repository;

use App\Entity\Session;
use App\Service\DateTimeService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    /**
     * @return Session[]
     */
    public function findAll(): array
    {
        $builder = $this->createQueryBuilder('session', 'session.id');

        return $builder->getQuery()->getResult();
    }

    public function get(string $sessionId): Session
    {
        $session = $this->findOneBy(['sessionId' => $sessionId]);

        if ($session === null) {
            $session = new Session();
            $session->setSessionId($sessionId);
        }

        return $session;
    }

    public function remove(string $id): void
    {
        $this->getEntityManager()->remove(
            $this->get($id)
        );
    }

    public function purge(): void
    {
        /** @var Session $session */
        foreach ($this->findAll() as $session) {
            $date = clone $session->getDate();

            if ($date->add($session->getLifetime()) >= DateTimeService::getCurrentUTC()) {
                $this->getEntityManager()->remove($session);
            }
        }

        $this->getEntityManager()->flush();
    }
}
