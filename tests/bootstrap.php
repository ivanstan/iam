<?php

use App\Command\DoctrineReloadCommand;
use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
    require dirname(__DIR__) . '/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

if ('test' === $_SERVER['APP_ENV']) {
    $kernel = new Kernel($_SERVER['APP_ENV'], true); // create a "test" kernel
    $kernel->boot();

    $command = new DoctrineReloadCommand($_SERVER['APP_ENV']);
    (new Application($kernel))->add($command);

    $command->run(
        new ArrayInput(
            [
                'command' => 'doctrine:reload',
                '--no-interaction' => true,
            ]
        ),
        new ConsoleOutput()
    );
}
