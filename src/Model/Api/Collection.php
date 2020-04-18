<?php

namespace App\Model\Api;

use Symfony\Component\Serializer\Annotation\Groups;

class Collection extends CollectionSpecification
{
    use ApiCollectionTrait;

    /**
     * @Groups("read")
     */
    protected array $members = [];

    public function getMembers(): array
    {
        return $this->members;
    }

    public function setMembers(array $members): void
    {
        $this->members = $members;
    }
}
