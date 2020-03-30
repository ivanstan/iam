<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Environment;

class MailerService
{
    private string $mailFrom;
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct($mailFrom, MailerInterface $mailer, Environment $twig)
    {
        $this->mailFrom = $mailFrom;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function send(TemplatedEmail $email): void
    {
        $body = $this->twig->render($email->getHtmlTemplate(), $email->getContext());

        /** @var Address $address */
        foreach ($email->getTo() as $address) {
            $message = (new \Swift_Message($email->getSubject()))
                ->setFrom($this->mailFrom)
                ->setTo($address->getEncodedAddress())
                ->setBody($body, 'text/html');
//             ToDo: add plain text   ->addPart(strip_tags($body), 'text/plain');

            $this->mailer->send($message);
        }
    }
}
