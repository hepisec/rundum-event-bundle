<?php

namespace Rundum\EventBundle\Maker;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Rundum\EventBundle\Enum\EventPriority;
use Rundum\EventBundle\Event\AbstractEntityEvent;
use Rundum\EventBundle\Enum\VerbPosition;
use Rundum\EventBundle\Event\CreateEntityEvent;
use Rundum\EventBundle\Event\DeleteEntityEvent;
use Rundum\EventBundle\Event\UpdateEntityEvent;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
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
        return 'Creates CUD events for entities';
    }

    /**
     * Configure the command: set description, input arguments, options, etc.
     *
     * By default, all arguments will be asked interactively. If you want
     * to avoid that, use the $inputConfig->setArgumentAsNonInteractive() method.
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig) {
        $command->addArgument('bound-class');
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
        
        $createEvent = $this->generateEvent($input, $generator, 'create', VerbPosition::BEFORE);
        $createdEvent = $this->generateEvent($input, $generator, 'created', VerbPosition::AFTER);
        $deleteEvent = $this->generateEvent($input, $generator, 'delete', VerbPosition::BEFORE);
        $deletedEvent = $this->generateEvent($input, $generator, 'deleted', VerbPosition::AFTER);
        $updateEvent = $this->generateEvent($input, $generator, 'update', VerbPosition::BEFORE);
        $updatedEvent = $this->generateEvent($input, $generator, 'updated', VerbPosition::AFTER);
               
        $this->generateEntityEventSubscriber($input, $generator, $createEvent, $createdEvent, $deleteEvent, $deletedEvent, $updateEvent, $updatedEvent);

        $generator->writeChanges();
        $this->writeSuccessMessage($io);
    }

    private function generateEvent(InputInterface $input, Generator $generator, string $verb, VerbPosition $verbPosition): ClassNameDetails {
        $boundClass = $input->getArgument('bound-class');
        $boundClassDetails = $generator->createClassNameDetails(
            $boundClass,
            'Entity\\'
        );        

        $classNameDetails = $generator->createClassNameDetails(
            ($verbPosition === VerbPosition::BEFORE) ? (ucfirst($verb) . $boundClass) : ($boundClass . ucfirst($verb)),
            'Event\\' . $boundClass,
            'Event'
        );

        $useStatements = new UseStatementGenerator([
            AbstractEntityEvent::class,
            $boundClassDetails->getFullName()
        ]);        

        $generator->generateClass($classNameDetails->getFullName(), __DIR__ . '/../Resources/skeleton/Event.tpl.php', [
            'use_statements' => $useStatements,
            'event_name' => strtolower($boundClassDetails->getShortName()) . '.' . $verb
        ]);

        return $classNameDetails;
    }

    private function generateEntityEventSubscriber(
        InputInterface $input, 
        Generator $generator, 
        ClassNameDetails $createEvent, 
        ClassNameDetails $createdEvent, 
        ClassNameDetails $deleteEvent, 
        ClassNameDetails $deletedEvent, 
        ClassNameDetails $updateEvent, 
        ClassNameDetails $updatedEvent) {
        $boundClass = $input->getArgument('bound-class');   

        $classNameDetails = $generator->createClassNameDetails(
            $boundClass,
            'EventSubscriber\\' . $boundClass,
            'EventSubscriber'
        );

        $useStatements = new UseStatementGenerator([
            EventPriority::class,
            CreateEntityEvent::class,
            DeleteEntityEvent::class,
            UpdateEntityEvent::class,
            $createEvent->getFullName(),
            $createdEvent->getFullName(),
            $deleteEvent->getFullName(),
            $deletedEvent->getFullName(),
            $updateEvent->getFullName(),
            $updatedEvent->getFullName()
        ]);        

        $generator->generateClass($classNameDetails->getFullName(), __DIR__ . '/../Resources/skeleton/EventSubscriber.tpl.php', [
            'use_statements' => $useStatements,
            'create_event' => $createEvent->getShortName(),
            'created_event' => $createdEvent->getShortName(),
            'delete_event' => $deleteEvent->getShortName(),
            'deleted_event' => $deletedEvent->getShortName(),
            'update_event' => $updateEvent->getShortName(),
            'updated_event' => $updatedEvent->getShortName()
        ]);
    }
}