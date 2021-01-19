<?php

namespace App\Entity\Behaviours;

use App\Service\DateTimeService;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait UpdatedAtTrait
{
    /**
     * @ORM\Column(type="datetime")
     */
    protected ?DateTime $updatedAt = null;

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdatedAt(): void
    {
        $this->updatedAt = DateTimeService::getCurrentUTC();
    }
}
