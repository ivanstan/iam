<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class UserProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['user'])]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['user'])]
    protected ?string $firstName = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['user'])]
    protected ?string $lastName = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['user'])]
    protected ?string $avatar = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }
}
