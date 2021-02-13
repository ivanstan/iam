<?php

namespace App\Command;

use App\Service\Generator\ReactEntityFormGenerator;
use App\Service\Generator\ReactEntityInterfaceGenerator;
use App\Service\Generator\ReactEntityRouteGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

class ReactEntityGeneratorCommand extends Command
{
    protected const ENTITY_ARGUMENT = 'entity';
    protected const FORCE_ARGUMENT = 'force';

    protected static $defaultName = 'generate:entity';

    public function __construct(
        protected ReactEntityInterfaceGenerator $interface,
        protected ReactEntityFormGenerator $form,
        protected ReactEntityRouteGenerator $route
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate react CRUD code for given entity.')
            ->addOption(
                self::ENTITY_ARGUMENT,
                null,
                InputOption::VALUE_OPTIONAL,
                'Entity for which to generate CRUD',
            )
            ->addOption(
                self::FORCE_ARGUMENT,
                '-f',
                InputOption::VALUE_OPTIONAL,
                'Force generation even if already exists. Files will be overwritten.',
                false,
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entity = $input->getOption(self::ENTITY_ARGUMENT);
        if ($entity === null) {
            $entity = $this->askForEntity($input, $output);
        }

        $fqn = $this->interface->getEntityFqnFromClassName($entity);
        if ($fqn === null) {
            throw new \InvalidArgumentException(\sprintf('Invalid %s "%s" provided as option.', self::ENTITY_ARGUMENT, $entity));
        }

        $className = $this->interface->getEntityClassNameFromFqn($fqn);
        $routeName = (new CamelCaseToSnakeCaseNameConverter())->normalize($className);

        if ($input->getOption(self::FORCE_ARGUMENT) === false && file_exists($this->interface->fileName($className))) {
            throw new \RuntimeException(sprintf('React entity %s already exists.', $className));
        }

        $this->interface->generate($fqn);
        $this->form->generate($fqn);
        $this->route->generate($fqn);

        $this->printRouteInstruction($output, $className, $routeName);

        return Command::SUCCESS;
    }

    protected function askForEntity(InputInterface $input, OutputInterface $output): string
    {
        $question = new Question('Please provide name of the entity for which you wish to generate CRUD: ');
        $question->setAutocompleterCallback(fn() => array_values($this->interface->getEntityList()));

        return $this->getHelper('question')->ask($input, $output, $question);
    }

    protected function printRouteInstruction(OutputInterface $output, $className, $routeName): void
    {
        $output->writeln('Add following to React router statement:');
        $output->writeln('');

        foreach (ReactEntityRouteGenerator::FILES as $file => $data) {
            $pageName = str_replace('{{className}}', $className, $file);
            $route = str_replace('{{routeName}}', $routeName, $data['route']);

            $output->writeln('<ProtectedRoute exact path="' . $route . '" condition={true} component={<' . $pageName . ' />} />');
        }
    }
}
