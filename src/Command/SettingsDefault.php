<?php

namespace App\Command;

use App\Entity\Settings;
use App\Model\Settings as SettingsModel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'settings:default', description: 'Revert settings to factory default'
)]
final class SettingsDefault extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        $repository = $this->em->getRepository(Settings::class);

        foreach ($repository->findAll() as $item) {
            $this->em->remove($item);
        }

        $this->em->flush();

        foreach (SettingsModel::$data as $item) {
            $entity = new Settings();
            $entity->setNamespace($item[0]);
            $entity->setName($item[1]);
            $entity->setValue($item[2]);

            $this->em->persist($entity);
        }

        $this->em->flush();

        $io->success('Settings reverted to factory default.');

        return self::SUCCESS;
    }
}
