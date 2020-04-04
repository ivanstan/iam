<?php

namespace App\Security;

use App\Entity\Token;
use App\Entity\User;
use App\Service\MailerService;
use App\Service\Traits\TranslatorAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class SecurityMailerService
{
    use TranslatorAwareTrait;

    protected string $appName;
    protected EntityManagerInterface $em;
    protected MailerService $mailer;
    protected TokenGenerator $generator;

    public function __construct(EntityManagerInterface $em, MailerService $mailer, TokenGenerator $generator, string $appName)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->generator = $generator;
        $this->appName = $appName;
    }

    /**
     * @throws \Exception
     */
    public function requestVerification(User $user): void
    {
        $token = new Token\UserVerificationToken($user);
        $this->em->persist($token);
        $this->em->flush();

        $subject = $this->translator->trans('Verify your account | %app_name%', ['%app_name%' => $this->appName]);

        $email = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate('email/verify.html.twig')
            ->context(
                [
                    'url' => $this->generator->generateUrl($token),
                    'subject' => $subject,
                ]
            );

        $this->mailer->send($email);
    }

    /**
     * @throws \Exception
     */
    public function requestRecovery(User $user): void
    {
        $token = new Token\UserRecoveryToken($user);
        $this->em->persist($token);
        $this->em->flush();

        $subject = $this->translator->trans('Password recovery | %app_name%', ['%app_name%' => $this->appName]);
        $email = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate('email/recovery.html.twig')
            ->context(
                [
                    'url' => $this->generator->generateUrl($token),
                    'subject' => $subject,
                ]
            );

        $this->mailer->send($email);
    }

    /**
     * @throws \Exception
     */
    public function invite(User $user): void
    {
        $token = new Token\UserInvitationToken($user);
        $this->em->persist($token);
        $this->em->flush();

        $subject = $this->translator->trans('You have invitation | %app_name%', ['%app_name%' => $this->appName]);
        $email = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate('email/invite.html.twig')
            ->context(
                [
                    'url' => $this->generator->generateUrl($token),
                    'subject' => $subject,
                ]
            );

        $this->mailer->send($email);
    }
}
