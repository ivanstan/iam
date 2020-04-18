<?php

namespace App\Repository;

use App\Entity\Settings;
use App\Model\Settings as SettingsModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class SettingsRepository extends ServiceEntityRepository
{
    public const REGISTRATION_ENABLED = 'registration_enabled';

    private array $cache = [];
    private string $namespace = Settings::DEFAULT_NAMESPACE;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Settings::class);
    }

    public function isRegistrationEnabled(): bool
    {
        return (bool)$this->getSettings('registration')->get('enabled', SettingsModel::getDefault('registration', 'enabled'));
    }

    public function getSettings(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function set(string $name, $value): void
    {
        /** @var Settings $entity */
        $entity = $this->cache[$this->namespace][$name] ?? null;

        if ($entity === null) {
            $entity = new Settings();
            $entity->setName($name);
            $entity->setNamespace($this->namespace);

            $this->getEntityManager()->persist($entity);
        }

        $entity->setValue($value);

        $this->cache[$this->namespace][$name] = $entity;

        $this->getEntityManager()->flush();

        $this->namespace = Settings::DEFAULT_NAMESPACE;
    }

    /**
     * @param string $name
     * @param $default
     *
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        if (empty($this->cache)) {
            $this->warmup();
        }

        /** @var Settings $entity */
        $entity = $this->cache[$this->namespace][$name] ?? null;

        if ($entity === null) {
            return $default;
        }

        $this->namespace = Settings::DEFAULT_NAMESPACE;

        return $entity->getValue();
    }

    private function warmup(): void
    {
        /** @var Settings $setting */
        foreach ($this->findAll() as $setting) {
            $this->cache[$setting->getNamespace()][$setting->getName()] = $setting;
        }
    }
}
