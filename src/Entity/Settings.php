<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SettingsRepository")
 * @ORM\Table(indexes={
 *  @ORM\Index(name="default", columns={"namespace","name"}),
 * })
 */
class Settings
{
    public const DEFAULT_NAMESPACE = 'root';

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     * @Assert\NotBlank();
     * @Assert\NotNull();
     */
    protected string $name;

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    protected string $namespace = self::DEFAULT_NAMESPACE;

    /**
     * @ORM\Column(type="text")
     */
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
