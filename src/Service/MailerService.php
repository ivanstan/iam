<?php

namespace App\Service;

use App\Entity\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService
{
    private string $mailFrom;
    protected EntityManagerInterface $entityManager;
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct($mailFrom, MailerInterface $mailer, Environment $twig, EntityManagerInterface $entityManager)
    {
        $this->mailFrom = $mailFrom;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->entityManager = $entityManager;
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

            $mail = new Mail();
            $mail->setFrom($this->mailFrom);
            $mail->setTo($address->getAddress());
            $mail->setSubject($email->getSubject());
            $mail->setBody($body);

            $this->entityManager->persist($mail);
            $this->entityManager->flush();

            try {
                $test = $this->mailer->send($message);
            } catch (TransportExceptionInterface $e) {
                print_r($e);
                // ToDo: log

            } finally {
            }
        }
    }
}
