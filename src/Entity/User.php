<?php

namespace App\Entity;

use App\Entity\Traits\CreatedTrait;
use App\Entity\Traits\UpdatedTrait;
use App\Security\Role;
use App\Service\DateTimeService;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface
{
    use CreatedTrait;
    use UpdatedTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email();
     * @Assert\NotBlank();
     * @Assert\NotNull();
     */
    private $email;

    /**
     * @ORM\Column(type="boolean", options={"default" : 1})
     */
    private bool $active = true;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private bool $verified = false;

    /** @var string */
    private $plainPassword;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserPreference", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $preference;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $avatar;

    /**
     * @var Session[]
     * @ORM\OneToMany(targetEntity="App\Entity\Session", mappedBy="user", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private array $sessions = [];

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt(): void
    {
        $this->created = DateTimeService::getCurrentUTC();
        $this->updated = DateTimeService::getCurrentUTC();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdated(): void
    {
        $this->updated = DateTimeService::getCurrentUTC();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = Role::USER;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        if (!\in_array(Role::USER, $roles, true)) {
            $roles[] = Role::USER;
        }

        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    public function eraseCredentials(): void
    {
        //        $this->plainPassword = null;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword): void
    {
        if (is_array($plainPassword)) {
            return;
        }

        $this->plainPassword = $plainPassword;
    }

    public function __toString(): string
    {
        $user = [
            'id' => $this->id,
            'email' => $this->email,
            'roles' => $this->getRoles(),
            'active' => $this->active,
            'displayName' => $this->getDisplayName(),
        ];

        return (string)json_encode($user, JSON_THROW_ON_ERROR);
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): void
    {
        $this->verified = $verified;
    }

    public function getPreference(): UserPreference
    {
        if ($this->preference) {
            return $this->preference;
        }

        return new UserPreference();
    }

    public function setPreference(UserPreference $preference): void
    {
        $this->preference = $preference;
    }

    public function getDisplayName(): string
    {
        return $this->getEmail();
    }
}
