<?php

namespace App\Entity;

use App\Entity\Behaviours\CreatedAtTrait;
use App\Service\DateTimeService;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MailRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Mail
{
    use CreatedAtTrait;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected ?int $id = null;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="`from`")
     * @Assert\Email();
     * @Assert\NotBlank();
     * @Assert\NotNull();
     */
    protected $from;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="`to`")
     * @Assert\Email();
     * @Assert\NotBlank();
     * @Assert\NotNull();
     */
    protected $to;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true, name="`subject`")
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected ?string $body = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function setFrom(?string $from): void
    {
        $this->from = $from;
    }

    public function getTo(): ?string
    {
        return $this->to;
    }

    public function setTo(?string $to): void
    {
        $this->to = $to;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): void
    {
        $this->body = $body;
    }
}
