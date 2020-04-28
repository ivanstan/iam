<?php

namespace App\Repository;

use App\Entity\Session;
use App\Entity\User;
use App\Model\Api\CollectionSpecification;
use App\Service\DateTimeService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

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

    public function collection(CollectionSpecification $specification): QueryBuilder
    {
        $builder = $this->createQueryBuilder('session', 'session.id');
        $builder->join('session.user', 'user');

        if ($specification->getQuery() !== null) {
            $builder
                ->where(
                    $builder->expr()->orX(
                        $builder->expr()->like('user.email', $builder->expr()->literal('%' . $specification->getQuery() . '%')),
                        $builder->expr()->like('session.ip', $builder->expr()->literal('%' . $specification->getQuery() . '%'))
                    )
                );
        }

        $builder->orderBy('session.' . $specification->getSort(), $specification->getSortDirection());

        return $builder;
    }

    public function getUserSessions(User $user): QueryBuilder
    {
        $builder = $this->createQueryBuilder('session');
        $builder->where('session.user = :user')->setParameter('user', $user);
        $builder->orderBy('session.ip');

        return $builder;
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
