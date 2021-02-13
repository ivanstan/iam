<?php

namespace App\Service\Generator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Twig\Environment;

class ReactEntityInterfaceGenerator extends AbstractGenerator
{
    use DoctrineEntityGeneratorTrait;

    public function __construct(protected EntityManagerInterface $em, protected Environment $twig)
    {
    }

    public function fileName($className): string {
        return "./assets/entity/${className}Interface.ts";
    }

    public function generate(string $fqn): void
    {
        $className = $this->getEntityClassNameFromFqn($fqn);
        $metadata = $this->em->getClassMetadata($fqn);
        $imports = $this->getImports($metadata);
        $content = $this->twig->render(
            'generator/entity/interface.ts.twig',
            [
                'imports' => $imports,
                'fields' => $this->getFields($metadata),
                'className' => $className,
            ]
        );

        $this->dump($this->fileName($className), $content);

        $content = $this->twig->render(
            'generator/entity/data-source.ts.twig',
            [
                'className' => $className,
                'propertyName' => (new CamelCaseToSnakeCaseNameConverter())->normalize($className),
            ]
        );

        $this->dump("./assets/data/${className}DataSource.ts", $content);
    }

    private function getImports(ClassMetadata $metadata)
    {
        $result = [];

        foreach ($metadata->getAssociationMappings() as $mapping) {
            $fileName = $this->getEntityClassNameFromFqn($mapping['targetEntity']);

            $result[] = 'import { ' . $fileName . 'Interface } from \'./' . $fileName . 'Interface\';';
        }

        return $result;
    }

    protected function getFields(ClassMetadata $metadata): array
    {
        $result = [];

        foreach ($metadata->getFieldNames() as $fieldName) {
            $result[$fieldName] = $this->getFieldType(
                $metadata->getFieldMapping($fieldName)
            );
        }

        foreach ($metadata->getAssociationMappings() as $mapping) {
            $result[$mapping['fieldName']] = $this->getAssociationType($mapping);
        }

        return $result;
    }

    protected function getFieldType(array $mapping): string
    {
        $type = $mapping['type'];

        $type = match ($type) {
            'json' => 'any[]',
            'integer' => 'number',
            'datetime', 'guid' => 'string',
            default => $type,
        };

        if ($mapping['nullable']) {
            $type .= ' | null';
        }

        return $type;
    }

    #[Pure] private function getAssociationType(array $mapping): string
    {
        $type = $this->getEntityClassNameFromFqn($mapping['targetEntity']) . 'Interface';

        if ($mapping['type'] === ClassMetadataInfo::ONE_TO_MANY || $mapping['type'] === ClassMetadataInfo::MANY_TO_MANY) {
            $type .= '[]';
        }

        return $type;
    }
}
