<?php

namespace Rundum\EventBundle\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * @author hendrik
 */
class MakeEntityEvents extends AbstractMaker {
    public function __construct(private DoctrineHelper $entityHelper)
    {
    }

    /**
     * Return the command name for your maker (e.g. make:report).
     */
    public static function getCommandName(): string {
        return 'make:entity-events';
    }

    public static function getCommandDescription(): string {
        return 'Creates CRUD events for entities';
    }

    /**
     * Configure the command: set description, input arguments, options, etc.
     *
     * By default, all arguments will be asked interactively. If you want
     * to avoid that, use the $inputConfig->setArgumentAsNonInteractive() method.
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig) {

    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void {
        if (null === $input->getArgument('bound-class')) {
            $entities = $this->entityHelper->getEntitiesForAutocomplete();

            $question = new Question('The name of Entity or fully qualified model class name that the new events will be bound to');
            $question->setValidator(fn ($answer) => Validator::entityExists($answer, $entities));
            $question->setAutocompleterValues($entities);
            $question->setMaxAttempts(3);

            $input->setArgument('bound-class', $io->askQuestion($question));
        }
    }

    /**
     * Configure any library dependencies that your maker requires.
     */
    public function configureDependencies(DependencyBuilder $dependencies) {
        $dependencies->addClassDependency(
            DoctrineBundle::class,
            'orm',
            false
        );
    }

    /**
     * Called after normal code generation: allows you to do anything.
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator) {

    }

}