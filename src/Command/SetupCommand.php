<?php


namespace App\Command;

use App\Entity\Application;
use App\Model\ApplicationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'iam:setup', description: 'Setup IAM'
)]
class SetupCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $application = new Application();
        $application->setName('IAM');
        $application->setUuid(ApplicationService::IAM_APP_UUID);
        $application->setUrl('localhost');
        $application->setRedirect('localhost');

        $this->em->persist($application);
        $this->em->flush();

        return self::SUCCESS;
    }
}
