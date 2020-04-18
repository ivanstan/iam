<?php

namespace App\Model\Api;

trait ApiCollectionTrait
{
    protected ?string $route = null;

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }
}
