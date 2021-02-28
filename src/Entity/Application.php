<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ApplicationRepository::class)
 */
class Application
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("user")
     */
    protected $id;

    /**
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     * @Groups("user")
     */
    protected $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("user")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url()
     * @Groups("user")
     */
    protected $url;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url()
     */
    protected $redirect;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="applications")
     * @ORM\JoinTable(name="application_users")
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity=Claim::class, mappedBy="application", orphanRemoval=true)
     */
    protected $claims = [];

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->claims = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getRedirect(): ?string
    {
        return rtrim($this->redirect, '/');
    }

    public function setRedirect(string $redirect): void
    {
        $this->redirect = $redirect;
    }

    /**
     * @return Collection|Claim[]
     */
    public function getClaims(): Collection
    {
        return $this->claims;
    }

    public function addClaim(Claim $claim): self
    {
        if (!$this->claims->contains($claim)) {
            $this->claims[] = $claim;
            $claim->setApplication($this);
        }

        return $this;
    }

    public function removeClaim(Claim $claim): self
    {
        if ($this->claims->removeElement($claim)) {
            // set the owning side to null (unless already changed)
            if ($claim->getApplication() === $this) {
                $claim->setApplication(null);
            }
        }

        return $this;
    }
}
