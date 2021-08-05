<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    public const DEFAULT_NAMESPACE = 'root';

    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Groups(['read'])]
    protected string $name;

    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    #[Groups(['read'])]
    protected string $namespace = self::DEFAULT_NAMESPACE;

    #[ORM\Column(type: 'text')]
    #[Groups(['read'])]
    protected ?string $value = null;

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getValue()
    {
        return unserialize($this->value, ['allowed_classes' => false]);
    }

    public function setValue($value): void
    {
        $this->value = serialize($value);
    }
}
