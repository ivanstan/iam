<?php

namespace App\Service\Generator;

use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\Pure;

trait DoctrineEntityGeneratorTrait
{
    /**
     * Cache result of getEntityList method, because of frequent calls to it.
     *
     * @var array|null
     */
    protected ?array $entityList = null;

    /**
     * @throws ORMException
     */
    public function getEntityFqnFromClassName(string $entity): ?string
    {
        if ($fqn = array_search($entity, $this->getEntityList(), true)) {
            return $fqn;
        }

        return null;
    }

    /**
     * Returns array of all registered Doctrine entities, where FQN is key and class name is value.
     *
     * @return array
     * @throws ORMException
     */
    public function getEntityList(): array
    {
        if ($this->entityList !== null) {
            return $this->entityList;
        }

        $list = $this->em->getConfiguration()->getMetadataDriverImpl()?->getAllClassNames();

        $result = [];

        if ($list === null) {
            return $result;
        }

        foreach ($list as $fqn) {
            $result[$fqn] = $this->getEntityClassNameFromFqn($fqn);
        }

        $this->entityList = $result;

        return $result;
    }

    #[Pure] public function getEntityClassNameFromFqn(string $fqn): string
    {
        return substr($fqn, strrpos($fqn, '\\') + 1);
    }

    public function getFile(string$file, string $className): string
    {
        return str_replace('{{className}}', $className, $file);
    }
}
