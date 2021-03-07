<?php

namespace App\Model\Api;

class CollectionSpecification
{
    use PageableCollectionTrait;
    use SortableCollectionTrait;

    protected ?string $query = null;

    protected string $entity;

    public function __construct(string $entity)
    {
        $this->entity = $entity;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(?string $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function setEntity(string $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}
