<?php

namespace App\Controller\Api;

use App\Model\Api\CollectionSpecification;
use App\Repository\EntityRepository;
use App\Service\CaseConverterTrait;
use App\Service\Util\DoctrineUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api')]
class EntityController extends AbstractController
{
    use CaseConverterTrait;

    public function __construct(
        protected EntityManagerInterface $em,
        protected DoctrineUtil $util,
        protected EntityRepository $repository,
        protected NormalizerInterface $normalizer,
    ) {
    }

    #[Route('/collection/{name}', name: "api_entity_collection")]
    public function collection(
        string $name
    ): JsonResponse {
        $fqn = $this->util->getEntityFqn($this->snakeCaseToCamelCase($name));

        // ToDo: check permission

        $spec = new CollectionSpecification($fqn);

        $collection = $this->repository->collection($spec);

        $data = [
            '@context' => 'http://www.w3.org/ns/hydra/context.jsonld',
        ];

        $data = array_merge($data, $this->normalizer->normalize($collection));

        return new JsonResponse($data);
    }

    #[Route('/collection/{name}/{id}', name: "api_entity_item")]
    public function item(
        string $name,
        int $id
    ): Response {
        $fqn = $this->util->getEntityFqn($this->snakeCaseToCamelCase($name));

        $entity = $this->em->getRepository($fqn)->find($id);

        // ToDo: check permission
        // ToDo: add permission hook

        $data = [
            '@context' => 'http://www.w3.org/ns/hydra/context.jsonld',
        ];

        $data = array_merge($data, $this->normalizer->normalize($entity), 'json', ['metadata' => true]);

        return new JsonResponse($data);
    }
}
