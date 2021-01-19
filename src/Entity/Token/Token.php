<?php

namespace App\Entity\Token;

use App\Entity\Behaviours\CreatedAtTrait;
use App\Security\TokenGenerator;
use App\Service\DateTimeService;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     UserToken::TYPE = "App\Entity\Token\UserToken",
 * })
 */
abstract class Token
{
    use CreatedAtTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    protected $token;

    /**
     * @var \DateInterval
     * @ORM\Column(name="`interval`", type="dateinterval", nullable=true)
     */
    protected $interval;

    /**
     * @throws \Exception
     */
    public function __construct(
        string $interval = TokenGenerator::TOKEN_INTERVAL,
        int $length = TokenGenerator::TOKEN_LENGTH
    ) {
        $this->created = DateTimeService::getCurrentUTC();
        $this->token = TokenGenerator::generate($length);
        $this->interval = new \DateInterval($interval);
    }

    /**
     * @throws \Exception
     */
    public function isValid(\DateTime $date): bool
    {
        if ($this->interval === null) {
            return true;
        }

        return $this->getCreatedAt()->add($this->interval) >= $date;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getInterval(): \DateInterval
    {
        return $this->interval;
    }

    public function setInterval(\DateInterval $interval): void
    {
        $this->interval = $interval;
    }
}
