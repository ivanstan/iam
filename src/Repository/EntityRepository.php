<?php

namespace App\Repository;

use App\Model\Api\Collection;
use App\Model\Api\CollectionSpecification;
use App\Service\Util\DoctrineUtil;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class EntityRepository
{
    public function __construct(protected EntityManagerInterface $em, protected DoctrineUtil $util)
    {
    }

    public function collection(CollectionSpecification $specification): Collection
    {
        $builder = $this->em->createQueryBuilder();

        $builder->select('e');
        $builder->from($specification->getEntity(), 'e');

        if ($specification->getQuery() !== null) {
            $builder
                ->where(
                    $builder->expr()->orX(
                        $builder->expr()->like('e.email', $builder->expr()->literal('%' . $specification->getQuery() . '%'))
                    )
                );
        }

        $total = $this->getCount($builder);

        if ($specification->getSort()) {
            $builder->orderBy('e.' . $specification->getSort(), $specification->getSortDirection());
        }

        $collection = new Collection($specification->getEntity());
        $collection->setTotal($total);

        $collection->setMembers($builder->getQuery()->getResult());

        return $collection;
    }

    private function getCount(QueryBuilder $builder): int
    {
        $builder = clone $builder;

        $builder->select('count(e.id)');

        return $builder->getQuery()->getSingleScalarResult();
    }
}
