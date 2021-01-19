<?php

namespace App\Entity\Behaviours;

use App\Service\DateTimeService;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait CreatedAtTrait
{
    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTime $createdAt = null;

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt(): void
    {
        $this->createdAt = DateTimeService::getCurrentUTC();
    }
}

