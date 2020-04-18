<?php


namespace App\DataFixtures;


use App\Entity\Session;
use App\Entity\User;
use App\Service\DateTimeService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SessionFixtures extends Fixture implements DependentFixtureInterface
{
    private const USER_AGENTS = [
        'Mozilla/5.0 (Linux; Android 8.0.0; SM-G960F Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.84 Mobile Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1',
        'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:15.0) Gecko/20100101 Firefox/15.0.1',
    ];

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll()->getQuery()->getResult();

        foreach ($users as $user) {
            for ($i = 0; $i < 4; $i++) {
                $session = new Session();
                $session->setSessionId(session_create_id('test'));
                $session->setUser($user);
                $session->setIp('127.0.0.' . $i);
                $session->setLastAccess(DateTimeService::getCurrentUTC());
                $session->setDate(DateTimeService::getCurrentUTC());
                $session->setLifetime(new \DateInterval('P1M'));
                $session->setUserAgent(self::USER_AGENTS[$i]);

                $manager->persist($session);
            }
        }

        $manager->flush();
    }
}
