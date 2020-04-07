<?php

namespace App\Repository;

use App\Entity\User;
use App\Model\CollectionSpecification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByEmail(string $email): ?User
    {
        $builder = $this->createQueryBuilder('u');

        $builder->where('u.email = :email')->setParameter('email', $email);

        try {
            return $builder->getQuery()->getSingleResult();
        } catch (NoResultException|NonUniqueResultException $exception) {
            return null;
        }
    }

    public function findAll($query = null): QueryBuilder
    {
        $builder = $this->createQueryBuilder('u');

        $builder->orderBy('u.email', 'ASC');

        if ($query !== null) {
            $builder->andWhere('u.email LIKE :query')->setParameter('query', '%' . $query . '%');
        }

        return $builder;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function collection(CollectionSpecification $specification): QueryBuilder
    {
        $builder = $this->createQueryBuilder('u');

        if ($specification->getQuery() !== null) {
            $builder
                ->where(
                    $builder->expr()->orX(
                        $builder->expr()->like('u.email', $builder->expr()->literal('%' . $specification->getQuery() . '%'))
                    )
                );
        }

        $builder->orderBy('u.' . $specification->getSort(), $specification->getSortDirection());

        return $builder;
    }
}
