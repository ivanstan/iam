<?php

namespace App\DataFixtures;

use App\Entity\Application;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ApplicationFixtures extends Fixture
{
    public const DEFAULT_APP_UUID = '822ea877-db3d-4f18-9453-1bca2da46f20';

    public function load(ObjectManager $manager)
    {
        $application = new Application();
        $application->setUuid(self::DEFAULT_APP_UUID);
        $application->setName('Default');
        $application->setUrl('https://local.default');
        $application->setRedirect('https://local.default');

        $manager->persist($application);
        $manager->flush();
    }
}
