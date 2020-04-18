<?php

namespace App\Model\Api;

class SearchableCollectionTrait
{
    protected ?string $search = null;
    protected array $searchFields = [];

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(?string $search): self
    {
        $this->search = $search;

        return $this;
    }

    public function getSearchFields(): array
    {
        return $this->searchFields;
    }

    public function setSearchFields(array $searchFields): self
    {
        $this->searchFields = $searchFields;

        return $this;
    }
}
