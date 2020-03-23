<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\Security\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'reference_admin_user';

    private const PASSWORD = 'test123';

    protected UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $password = $this->encoder->encodePassword(new User(), self::PASSWORD);

        // Add admin user
        $user = new User();
        $user->setEmail('admin@example.com');
        $user->setRoles([Role::ADMIN]);
        $user->setActive(true);
        $user->setPassword($password);
        $manager->persist($user);

        $this->addReference(self::ADMIN_USER_REFERENCE, $user);

        $user = new User();
        $user->setEmail('user1@example.com');
        $user->setRoles([Role::USER]);
        $user->setActive(false);
        $user->setPassword($password);
        $manager->persist($user);

        for($i = 2; $i < 50; $i++) {
            $user = new User();
            $user->setEmail("user$i@example.com");
            $user->setRoles([Role::USER]);
            $user->setActive(true);
            $user->setPassword($password);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
