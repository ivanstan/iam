<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SessionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Session
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string", length=128, unique=true)
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User",inversedBy="sessions")
     */
    private $user;

    /**
     * @var mixed
     * @ORM\Column(type="blob", nullable=true)
     */
    private $data;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var \DateInterval
     * @ORM\Column(type="dateinterval")
     */
    private $lifetime;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups("read")
     */
    private $lastAccess;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Groups("read")
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $userAgent;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $connection): void
    {
        $this->user = $connection;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getLifetime(): \DateInterval
    {
        return $this->lifetime;
    }

    public function setLifetime(\DateInterval $lifetime): void
    {
        $this->lifetime = $lifetime;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    public function getLastAccess(): ?\DateTime
    {
        return $this->lastAccess;
    }

    public function setLastAccess(\DateTime $lastAccess): void
    {
        $this->lastAccess = $lastAccess;
    }
}
