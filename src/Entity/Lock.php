<?php

namespace App\Entity;

use App\Repository\LockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LockRepository::class)]
#[ORM\Table(name: '`lock`')]
class Lock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    protected $name;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    protected $value;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    protected $data;

    /**
     * @var \DateTime
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    protected $expire;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): void
    {
        $this->data = $data;
    }

    public function getExpire(): ?\DateTime
    {
        return $this->expire;
    }

    public function setExpire(?\DateTime $expire): void
    {
        $this->expire = $expire;
    }
}
