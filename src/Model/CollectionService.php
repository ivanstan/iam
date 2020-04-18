<?php

namespace App\Model;

use App\Model\Api\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CollectionService
{
    protected EntityManagerInterface $em;
    protected NormalizerInterface $normalizer;
    protected RouterInterface $router;

    public function __construct(EntityManagerInterface $em, RouterInterface $router, NormalizerInterface $normalizer)
    {
        $this->em = $em;
        $this->normalizer = $normalizer;
        $this->router = $router;
    }

    public function collection(Collection $collection): Collection
    {
        $builder = $this->em->createQueryBuilder();

        $builder->from($collection->getEntity(), 'a');
        $builder->select('a');

        if ($collection->getSort()) {
            $builder->orderBy('a.' . $collection->getSort(), $collection->getSortDirection());
        }

        $total = \count($builder->getQuery()->getResult());

        $builder->setMaxResults($collection->getPageSize());
        $builder->setFirstResult($collection->getOffset());

        $collection->setMembers($builder->getQuery()->getResult());
        $collection->setTotal($total);

        return $collection;
    }

    public function normalize(Collection $collection, Request $request)
    {
        $members = $this->normalizer->normalize($collection->getMembers(), 'json', ['groups' => 'read']);

        $normalized = [
            'total' => $collection->getTotal(),
            'members' => $members,
        ];

        if ($collection->getRoute() !== null) {
            $normalized['view'] = $this->getPagination($collection, $request);
        }

        return $normalized;
    }

    private function getPagination(Collection $collection, Request $request): array
    {
        $params = $request->query->all();

        $result = [
            'first' => $this->router->generate(
                $request->attributes->get('_route'),
                array_merge($params, ['page' => 1]),
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'previous' => $this->router->generate(
                $request->attributes->get('_route'),
                array_merge($params, ['page' => $collection->getPreviousPage()]),
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'next' => $this->router->generate(
                $request->attributes->get('_route'),
                array_merge($params, ['page' => $collection->getNextPage()]),
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'last' => $this->router->generate(
                $request->attributes->get('_route'),
                array_merge($params, ['page' => $collection->getPageCount()]),
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ];

        if ($collection->getPage() === 1) {
            $result['previous'] = null;
        }

        if ($collection->getPage() === $collection->getNextPage()) {
            $result['next'] = null;
        }

        return $result;
    }
}
