<?php

namespace App\Controller\Api;

use App\Service\CaseConverterTrait;
use App\Service\Generator\DoctrineEntityGeneratorParameter;
use App\Service\Util\ClassUtil;
use App\Service\Util\DoctrineUtil;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/api")
 */
class MetadataController extends AbstractController
{
    use CaseConverterTrait;

    public function __construct(protected DoctrineUtil $util)
    {
    }

    /**
     * @Route(path="/entity", methods={"GET"})
     * @SWG\Tag(name="Entity")
     * @SWG\Response(
     *     response=200,
     *     description="Returns list of declared entites."
     * )
     */
    public function entity(): JsonResponse
    {
        $entityList = $this->getDoctrine()->getManager()->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

        $data = [];
        foreach ($entityList as $fqn) {
            $className = ClassUtil::getClassNameFromFqn($fqn);

            $data[] = [
                '@id' => $this->generateUrl(
                    'api_entity_metadata',
                    ['entity' => $this->camelCaseToSnakeCase($className)],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                '@type' => 'Entity',
                'name' => $className,
            ];
        }

        return new JsonResponse(
            $data,
        );
    }

    /**
     * @Route(path="/entity/{entity}", methods={"GET"}, name="api_entity_metadata")
     * @SWG\Tag(name="Entity")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the entity metadata.",
     * )
     * @SWG\Parameter(
     *     name="entity",
     *     in="query",
     *     type="string",
     *     description="Entity FQN."
     * )
     */
    public function metadata(string $entity): JsonResponse
    {
        $metadata = $this->getDoctrine()->getManager()->getClassMetadata($this->util->getEntityFqn($this->snakeCaseToCamelCase($entity)));

        $param = new DoctrineEntityGeneratorParameter($metadata);

        return new JsonResponse(
            [
                'name' => ClassUtil::getClassNameFromFqn($metadata->getName()),
                'identifier' => $metadata->getIdentifierFieldNames(),
                'fields' => $param->getFields(),
            ]
        );
    }
}
