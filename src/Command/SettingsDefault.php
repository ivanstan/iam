<?php

namespace App\Command;

use App\Entity\Settings;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Model\Settings as SettingsModel;

class SettingsDefault extends Command
{
    protected static $defaultName = 'settings:default';

    protected EntityManagerInterface $em;

    public function __construct(string $name = null, EntityManagerInterface $em)
    {
        parent::__construct($name);
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Revert settings to factory default.');
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

        return 0;
    }
}
