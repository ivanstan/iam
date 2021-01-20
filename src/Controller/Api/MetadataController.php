<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class MetadataController extends AbstractController
{
    /**
     * @Route(path="/entities")
     */
    public function entities(): JsonResponse
    {
        return new JsonResponse(
            [
                $this->getDoctrine()->getManager()->getConfiguration()->getMetadataDriverImpl()->getAllClassNames(),
            ]
        );
    }

    /**
     * @Route(path="/metadata")
     */
    public function metadata(Request $request): JsonResponse
    {
        $entity = $request->get('entity');

        if (!in_array($entity, $this->getDoctrine()->getManager()->getConfiguration()->getMetadataDriverImpl()->getAllClassNames(), true)) {
            throw new \RuntimeException(sprintf('Non existing entity "%s"', $entity));
        }

        $metadata = $this->getDoctrine()->getManager()->getClassMetadata($entity);

        return new JsonResponse(
            [
                'name' => $metadata->getName(),
                'identifier' => $metadata->getIdentifierFieldNames(),
                'fields' => array_merge($metadata->fieldMappings, $metadata->associationMappings),
            ]
        );
    }
}
