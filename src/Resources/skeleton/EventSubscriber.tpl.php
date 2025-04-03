<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

class <?= $class_name; ?> implements EventSubscriberInterface {

    public static function getSubscribedEvents(): array {
        return [
            <?= $create_event ?>::getName() => [
                ['create', EventPriority::FIRST->value],
                ['dispatchCreated', EventPriority::LAST->value]
            ],
            <?= $update_event ?>::getName() => [
                ['update', EventPriority::FIRST->value],
                ['dispatchUpdated', EventPriority::LAST->value]
            ],            
            <?= $delete_event ?>::getName() => [
                ['delete', EventPriority::FIRST->value],
                ['dispatchDeleted', EventPriority::LAST->value]
            ]
        ];
    }

    public function __construct(
        private LoggerInterface $logger,
        private EventDispatcherInterface $dispatcher
    ) {
    }

    public function create(<?= $create_event ?> $event): void {
        $this->dispatcher->dispatch(new CreateEntityEvent($event->getEntity()), CreateEntityEvent::getName());
    }

    public function dispatchCreated(<?= $create_event ?> $event): void {
        $this->dispatcher->dispatch(new <?= $created_event ?>($event->getEntity()), <?= $created_event ?>::getName());
    }

    public function update(<?= $update_event ?> $event): void {
        $this->dispatcher->dispatch(new UpdateEntityEvent($event->getEntity()), UpdateEntityEvent::getName());
    }

    public function dispatchUpdated(<?= $update_event ?> $event): void {
        $this->dispatcher->dispatch(new <?= $updated_event ?>($event->getEntity()), <?= $updated_event ?>::getName());
    }

    public function delete(<?= $delete_event ?> $event): void {
        $this->dispatcher->dispatch(new DeleteEntityEvent($event->getEntity()), DeleteEntityEvent::getName());
    }

    public function dispatchDeleted(<?= $delete_event ?> $event): void {
        $this->dispatcher->dispatch(new <?= $deleted_event ?>($event->getEntity()), <?= $deleted_event ?>::getName());
    }
}