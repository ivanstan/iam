<?php

namespace App\Entity\Traits;

trait UpdatedTrait
{
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $updated;

    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $updated): void
    {
        $this->updated = $updated;
    }
}
