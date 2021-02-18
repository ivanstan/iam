<?php

namespace App\Serializer;

use App\Service\CaseConverterTrait;
use App\Service\Generator\DoctrineEntityGeneratorParameter;
use App\Service\Util\ClassUtil;
use App\Service\Util\DoctrineUtil;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class EntityApiNormalizer implements ContextAwareNormalizerInterface
{
    use CaseConverterTrait;

    public function __construct(
        protected EntityManagerInterface $em,
        protected UrlGeneratorInterface $router,
        protected ObjectNormalizer $normalizer,
        protected DoctrineUtil $util,
    ) {
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $data = [];
        $className = ClassUtil::getClassNameFromFqn(get_class($object));

        $metadata = $this->em->getClassMetadata(get_class($object));
        $param = new DoctrineEntityGeneratorParameter($metadata);

        $data['@id'] = $this->getEntityUrl($object, $metadata);
        $data['@type'] = $className;

        $normalized = $this->normalizer->normalize($object, $format, $context);

        if (($context['metadata'] ?? false) === true) {
            $data['@meta'] = $this->getRequestedMeta($param, $normalized);
        }

        $data = array_merge($data, $normalized);

        return $data;
    }

    protected function getEntityUrl($entity, ClassMetadata $metadata): ?string
    {
        $identifierGetter = 'get' . ucfirst($metadata->getIdentifier()[0] ?? '');
        $className = ClassUtil::getClassNameFromFqn(get_class($entity));

        try {
            if (!method_exists($entity, $identifierGetter)) {
                return null;
            }

            return $this->router->generate(
                'api_entity_item',
                [
                    'name' => $this->camelCaseToSnakeCase($className),
                    'id' => $entity->$$identifierGetter,
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Compares normalized entity and available fields only to return meta for fields that are
     * present in normalized entity. We want to avoid exposing filed information for fields ignored fields.
     */
    public function getRequestedMeta(DoctrineEntityGeneratorParameter $metadata, array $normalized): array
    {
        $meta = $metadata->getFields();

        $data = [];
        foreach ($normalized as $fieldName => $item) {
            if (isset($meta[$fieldName])) {
                $data[$fieldName] = $meta[$fieldName];
            }
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        if (!is_object($data)) {
            return false;
        }

        return $this->util->isDoctrineEntity(get_class($data));
    }
}
