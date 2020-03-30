<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SettingsRepository")
 */
class Settings
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank();
     * @Assert\NotNull();
     */
    private string $name;

    /**
     * @ORM\Column(type="text")
     */
    private $value;

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
