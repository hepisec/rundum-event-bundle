<?php

namespace Rundum\EventBundle\EventSubscriber;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Rundum\EventBundle\Contracts\CreateEventInterface;
use Rundum\EventBundle\Contracts\EntityEventInterface;
use Rundum\EventBundle\Enum\EventPriority;
use Rundum\EventBundle\Event\AbstractEntityEvent;
use Rundum\EventBundle\Event\CreateEntityEvent;
use Rundum\EventBundle\Event\DeleteEntityEvent;
use Rundum\EventBundle\Event\EntityCreatedEvent;
use Rundum\EventBundle\Event\EntityDeletedEvent;
use Rundum\EventBundle\Event\EntityUpdatedEvent;
use Rundum\EventBundle\Event\UpdateEntityEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Hendrik Pilz <pilz@rundum.digital>
 */
class EntityEventSubscriber implements EventSubscriberInterface {

    public static function getSubscribedEvents(): array {
        return [
            CreateEntityEvent::getName() => [
                ['createOrUpdate', EventPriority::FIRST]
            ],
            UpdateEntityEvent::getName() => [
                ['createOrUpdate', EventPriority::FIRST]
            ],            
            DeleteEntityEvent::getName() => [
                ['delete', EventPriority::FIRST]
            ]
        ];
    }

    private $logger;
    private $doctrine;
    private $dispatcher;

    public function __construct(
            LoggerInterface $logger,
            ManagerRegistry $doctrine,
            EventDispatcherInterface $dispatcher
    ) {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
        $this->dispatcher = $dispatcher;
    }

    private function getEntityManager(): EntityManager {
        return $this->doctrine->getManager();
    }

    /**
     * Create or update an entity
     *
     * Dispatches EntityChangeCompletedEvent on success or EntityChangeFailedEvent on failure
     *
     * @param AbstractEntityEvent $event
     * @return void
     */
    public function createOrUpdate(AbstractEntityEvent $event): void {
        $entity = $event->getEntity();
        $entities = is_array($entity) ? $entity : [$entity];

        $em = $this->getEntityManager();

        try {
            foreach ($entities as $entity) {
                if ($event instanceof CreateEventInterface) {
                    $this->logger->debug('Persisting new entity of type ' . get_class($entity));
                    $em->persist($entity);
                } else {
                    $this->logger->debug('Updating entity of type ' . get_class($entity));
                }
            }

            $em->flush();

            if ($event instanceof CreateEventInterface) {
                $this->dispatcher->dispatch(new EntityCreatedEvent($event->getEntity()), EntityCreatedEvent::getName());
            } else {
                $this->dispatcher->dispatch(new EntityUpdatedEvent($event->getEntity()), EntityUpdatedEvent::getName());
            }
        } catch (\Exception $ex) {
            $this->logger->warning('Operation failed: ' . $ex->getMessage());
            $this->logger->warning($ex->getTraceAsString());
            $event->stopPropagation();
        }
    }

    /**
     * Remove an entity
     *
     * Dispatches EntityRemovalCompletedEvent on success or EntityRemovalFailedEvent on failure
     *
     * @param AbstractEntityEvent $event
     * @return void
     */
    public function delete(AbstractEntityEvent $event): void {
        $entity = $event->getEntity();
        $entities = is_array($entity) ? $entity : [$entity];

        $em = $this->getEntityManager();

        try {
            foreach ($entities as $entity) {
                $this->logger->debug('Deleting entity of type ' . get_class($entity));
                $em->remove($entity);
            }

            $em->flush();
            $this->dispatcher->dispatch(new EntityDeletedEvent($event->getEntity()), EntityDeletedEvent::getName());
        } catch (\Exception $ex) {
            $this->logger->warning('Operation failed: ' . $ex->getMessage());
            $this->logger->warning($ex->getTraceAsString());
            $event->stopPropagation();
        }
    }

}