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
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("read")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, unique=true)
     */
    protected $sessionId;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    protected $user;

    /**
     * @var mixed
     * @ORM\Column(type="blob", nullable=true)
     */
    protected $data;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Groups("read")
     */
    protected $date;

    /**
     * @var \DateInterval
     * @ORM\Column(type="dateinterval")
     * @Groups("read")
     */
    protected $lifetime;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups("read")
     */
    protected $lastAccess;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Groups("read")
     */
    protected $ip;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     * @Groups("read")
     */
    protected $userAgent;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): void
    {
        $this->sessionId = $sessionId;
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
