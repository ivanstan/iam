<?php

namespace App\Repository;

use App\Entity\Settings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class SettingsRepository extends ServiceEntityRepository
{
    public const REGISTRATION_ENABLED = 'registration_enabled';

    private array $cache = [];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Settings::class);
    }

    public function isRegistrationEnabled(): bool
    {
        return (bool) ($this->get(self::REGISTRATION_ENABLED, true) ?? true);
    }

    public function set(string $name, $value): void
    {
        /** @var Settings $entity */
        $entity = $this->find($name);

        if ($entity === null) {
            $entity = new Settings();
            $entity->setName($name);

            $this->getEntityManager()->persist($entity);
        }

        $entity->setValue($value);

        $this->cache[$name] = $entity;

        $this->getEntityManager()->flush();
    }

    /**
     * @param string $name
     * @param $default
     *
     * @return mixed
     */
    public function get(string $name, $default)
    {
        if (empty($this->cache)) {
            $this->warmup();
        }

        /** @var Settings $entity */
        $entity = $this->cache[$name] ?? $this->find($name);

        if ($entity === null) {
            return $default;
        }

        return $entity->getValue();
    }

    private function warmup(): void
    {
        /** @var Settings $setting */
        foreach ($this->findAll() as $setting) {
            $this->cache[$setting->getName()] = $setting;
        }
    }
}
