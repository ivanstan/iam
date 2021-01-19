<?php

namespace App\Entity;

use App\Entity\Behaviours\CreatedAtTrait;
use App\Entity\Behaviours\UpdatedAtTrait;
use App\Security\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    use CreatedAtTrait;
    use UpdatedAtTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read", "jwt"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email();
     * @Assert\NotBlank();
     * @Assert\NotNull();
     * @Groups({"read", "jwt"})
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
     * @Groups({"read", "jwt"})
     */
    protected array $roles = [];

    /**
     * Used to indicate if user is active. Inactive users are pending for account delete or have set their account for temporary
     * inactivity.
     * Use to hide user's content or profile when set inactive.
     *
     * @ORM\Column(type="boolean", options={"default" : 1})
     * @Groups({"read", "jwt"})
     */
    protected bool $active = true;

    /**
     * If true user is indeed owner of account self::email.
     * Use to restrict publishing of content created by users that are not verified.
     *
     * @ORM\Column(type="boolean", options={"default" : 0})
     * @Groups({"read", "jwt"})
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
     * @ORM\OneToOne(targetEntity="App\Entity\UserPreference", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups("read")
     */
    protected $preference;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserProfile", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups("read")
     */
    protected $profile;

    /**
     * @var Session[]
     * @ORM\OneToMany(targetEntity="App\Entity\Session", mappedBy="user", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups("read")
     */
    protected $sessions;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Application", mappedBy="users")
     */
    protected $applications;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
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
        if ($this->getProfile()->getFirstName() && $this->getProfile()->getLastName()) {
            return $this->getProfile()->getFirstName() . ' ' . $this->getProfile()->getLastName();
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
        return $this->preference ?? new UserPreference();
    }

    public function setPreference(UserPreference $preference): void
    {
        $this->preference = $preference;
    }

    public function getProfile(): UserProfile
    {
        return $this->profile ?? new UserProfile();
    }

    public function setProfile(UserProfile $profile): void
    {
        $this->profile = $profile;
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

    /**
     * @return Collection|Application[]
     */
    public function getApplications(): array
    {
        return $this->applications->toArray();
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications[] = $application;
            $application->addUser($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->removeElement($application)) {
            $application->removeUser($this);
        }

        return $this;
    }
}
