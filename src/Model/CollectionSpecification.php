<?php

namespace App\Model;

class CollectionSpecification
{
    protected ?string $query = null;
    protected ?string $sort = null;
    protected string $sortDirection = 'ASC';

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(?string $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function getSort(): ?string
    {
        return $this->sort;
    }

    public function setSort(?string $sort): self
    {
        if ($sort !== null) {
            $this->sort = $sort;
        }

        return $this;
    }

    public function getSortDirection(): string
    {
        return $this->sortDirection;
    }

    public function setSortDirection(?string $sortDirection): self
    {
        if ($sortDirection !== null) {
            $this->sortDirection = $sortDirection;
        }

        return $this;
    }
}
