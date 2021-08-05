<?php

namespace App\DataFixtures;

use App\Entity\Mail;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Twig\Environment;

class MailFixtures extends Fixture
{
    public function __construct(protected Environment $twig)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $body = $this->twig->render('email/invite.html.twig');

        for ($i = 1; $i <= 50; $i++) {
            $mail = new Mail();
            $mail->setFrom('admin@example.com');
            $mail->setTo('user1@example.com');
            $mail->setSubject('Example subject');
            $mail->setBody($body);

            $manager->persist($mail);
        }

        $manager->flush();
    }
}
