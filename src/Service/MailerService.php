<?php

namespace App\Service;

use App\Entity\Mail;
use App\Service\Traits\LoggerAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected string $mailFrom;
    protected EntityManagerInterface $em;
    protected MailerInterface $mailer;
    protected Environment $twig;

    public function __construct($mailFrom, MailerInterface $mailer, Environment $twig, EntityManagerInterface $entityManager)
    {
        $this->mailFrom = $mailFrom;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->em = $entityManager;
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
                $this->logger->warning(sprintf('Unable to send mail with exception: %s', $e->getMessage()));
            } finally {
                $mail = new Mail();
                $mail->setFrom($this->mailFrom);
                $mail->setTo($address->getAddress());
                $mail->setSubject($email->getSubject());
                $mail->setBody($body);

                $this->em->persist($mail);
                $this->em->flush();
            }
        }
    }
}
