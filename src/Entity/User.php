<?php

namespace App\Entity;

use App\Entity\Traits\CreatedTrait;
use App\Entity\Traits\UpdatedTrait;
use App\Security\Role;
use App\Service\DateTimeService;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
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
     * @Groups("read")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email();
     * @Assert\NotBlank();
     * @Assert\NotNull();
     * @Groups("read")
     */
    protected $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    protected $password;

    /**
     * @var string
     * @Groups("read")
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="json")
     * @Groups("read")
     */
    protected array $roles = [];

    /**
     * Used to indicate if user is active. Inactive users are pending for account delete or have set their account for temporary
     * inactivity.
     * Use to hide user's content or profile when set inactive.
     *
     * @ORM\Column(type="boolean", options={"default" : 1})
     * @Groups("read")
     */
    protected bool $active = true;

    /**
     * If true user is indeed owner of account self::email.
     * Use to restrict publishing of content created by users that are not verified.
     *
     * @ORM\Column(type="boolean", options={"default" : 0})
     * @Groups("read")
     */
    protected bool $verified = false;

    /**
     * Used for denying misbehaving users access to platform. If true user won't be able to login.
     *
     * @ORM\Column(type="boolean", options={"default" : 0})
     * @Groups("read")
     */
    protected bool $banned = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $avatar;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserPreference", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups("read")
     */
    protected $preference;

    /**
     * @var Session[]
     * @ORM\OneToMany(targetEntity="App\Entity\Session", mappedBy="user", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups("read")
     */
    protected $sessions;

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

    public function eraseCredentials(): void
    {
        //        $this->plainPassword = null;
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
        if (\is_array($plainPassword)) {
            return;
        }

        $this->plainPassword = $plainPassword;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = Role::USER;

        return array_values(array_unique($roles));
    }

    public function setRoles(array $roles): self
    {
        if (!\in_array(Role::USER, $roles, true)) {
            $roles[] = Role::USER;
        }

        $this->roles = $roles;

        return $this;
    }

    public function getDisplayName(): string
    {
        if ($this->firstName && $this->lastName) {
            return $this->firstName . ' ' . $this->lastName;
        }

        return $this->getEmail();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
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

    public function isBanned(): bool
    {
        return $this->banned;
    }

    public function setBanned(bool $banned): void
    {
        $this->banned = $banned;
    }

    /**
     * @return Session[]
     */
    public function getSessions(): array
    {
        return $this->sessions->toArray();
    }
}
