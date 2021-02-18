<?php

namespace App\Service\Generator;

use App\Service\Util\ClassUtil;
use App\Service\Util\DoctrineUtil;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

class DoctrineEntityGeneratorParameter
{
    public function __construct(protected ClassMetadata $metadata)
    {
        $this->className = ClassUtil::getClassNameFromFqn($this->getFQN());
    }

    public function getFields(): array
    {
        $fields = [];
        foreach ($this->metadata->getAssociationMappings() as $name => $association) {
            if (DoctrineUtil::isAssociationCollection($association)) {
                $fields[$name] = [
                    'type' => 'array',
                    'target' => ClassUtil::getClassNameFromFqn($association['targetEntity']),
                ];

                continue;
            }
        }

        foreach ($this->metadata->getFieldNames() as $name => $fieldName) {
            $mapping = $this->metadata->getFieldMapping($fieldName);

            $fields[$mapping['fieldName']] = [
                'type' => $mapping['type'],
                'nullable' => $mapping['nullable'],
            ];
        }

        return $fields;
    }

    public function getFQN(): string
    {
        return $this->metadata->getName();
    }

    public function getPropertyName(): string
    {
        return str_replace('-', '_', $this->getRouteName());
    }

    public function getRouteName(): string
    {
        return (new CamelCaseToSnakeCaseNameConverter())->normalize($this->getClassName());
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getMetaData(): ClassMetadata
    {
        return $this->metadata;
    }
}
