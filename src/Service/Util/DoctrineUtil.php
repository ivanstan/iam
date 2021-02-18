<?php

namespace App\Service\Util;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\ORMException;

class DoctrineUtil
{
    protected ?array $entityList = null;

    public function __construct(protected EntityManagerInterface $em)
    {
    }

    public function isDoctrineEntity(string $fqn): bool
    {
        $entityList = $this->getEntityList();

        return isset($entityList[$fqn]);
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

        $list = $this->em->getConfiguration()?->getMetadataDriverImpl()?->getAllClassNames();

        foreach ($list as $fqn) {
            $this->entityList[$fqn] = ClassUtil::getClassNameFromFqn($fqn);
        }

        return $this->entityList;
    }

    public function getEntityFqn(string $className): ?string
    {
        if ($fqn = array_search($className, $this->getEntityList(), true)) {
            return $fqn;
        }

        return null;
    }

    public static function isAssociationCollection(array $association): bool
    {
        return $association['type'] === ClassMetadataInfo::ONE_TO_MANY || $association['type'] === ClassMetadataInfo::MANY_TO_MANY;
    }
}
