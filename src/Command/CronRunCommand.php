<?php

namespace App\Command;

use App\Entity\Token\UserAccessToken;
use App\Entity\Token\UserDeleteToken;
use App\Entity\Token\UserEmailChangeToken;
use App\Entity\Token\UserInvitationToken;
use App\Entity\Token\UserRecoveryToken;
use App\Entity\Token\UserVerificationToken;
use App\Service\DateTimeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CronRunCommand extends Command
{
    protected static $defaultName = 'cron:run';

    protected EntityManagerInterface $em;

    public function __construct(string $name = null, EntityManagerInterface $em)
    {
        parent::__construct($name);
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Default cron job');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->deleteInvalidTokens(UserAccessToken::class);
        $this->deleteInvalidTokens(UserEmailChangeToken::class);
        $this->deleteInvalidTokens(UserInvitationToken::class);
        $this->deleteInvalidTokens(UserRecoveryToken::class);
        $this->deleteInvalidTokens(UserVerificationToken::class);

        // ToDo: remove expired items from ban table.

        $io->success('Cron has been executed.');

        return Command::SUCCESS;
    }

    protected function deleteInvalidTokens(string $type): void
    {
        $tokens = $this->em->getRepository($type)->findAll();

        foreach ($tokens as $token) {
            if (!$token->isValid(DateTimeService::getCurrentUTC())) {
                $this->em->remove($token);

                // ToDo: refactor to event
                if ($token instanceof UserDeleteToken) {
                    $this->em->remove($token->getUser());
                }
            }
        }

        $this->em->flush();
    }
}
