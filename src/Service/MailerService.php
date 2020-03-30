<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
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
            $message = (new Email())
                ->from($this->mailFrom)
                ->to($address->getEncodedAddress())
                ->subject($email->getSubject())
                ->html($body);

            try {
                $this->mailer->send($message);
            } catch (TransportExceptionInterface $e) {

                print_r($e);

                // ToDo: log

            }
        }
    }
}
