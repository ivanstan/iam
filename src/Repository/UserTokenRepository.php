<?php

namespace App\Repository;

use App\Entity\Token\UserToken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserToken::class);
    }

    public function getUserTokens(User $user, string $type)
    {
        return $this->createQueryBuilder('t')
            ->select('t', 'u')
            ->join('t.user', 'u')
            ->andWhere('t.user = :user')->setParameter('user', $user)
            ->andWhere('t INSTANCE OF :type')->setParameter('type', $this->getEntityManager()->getClassMetadata($type))
            ->getQuery()
            ->getResult();
    }

    public function getToken(string $token, string $type): ?UserToken
    {
        $builder = $this->createQueryBuilder('t')
            ->select('t', 'u')
            ->join('t.user', 'u')
            ->where('t.token = :token')->setParameter('token', $token)
            //->andWhere('t INSTANCE OF :type')->setParameter('type', $this->getEntityManager()->getClassMetadata($type))
        ;

        $result = $builder->getQuery()->getResult();

        return $result[0] ?? null;
    }
}
