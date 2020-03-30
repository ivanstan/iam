<?php

namespace App\Entity\Traits;

trait CreatedTrait
{
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $created;

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): void
    {
        $this->created = $created;
    }
}
