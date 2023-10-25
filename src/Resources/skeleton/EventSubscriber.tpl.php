<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

class <?= $class_name; ?> implements EventSubscriberInterface {

    public static function getSubscribedEvents(): array {
        return [
            <?= $create_event ?>::getName() => [
                ['create', EventPriority::FIRST],
                ['dispatchCreated', EventPriority::LAST]
            ],
            <?= $update_event ?>::getName() => [
                ['update', EventPriority::FIRST],
                ['dispatchUpdated', EventPriority::LAST]
            ],            
            <?= $delete_event ?>::getName() => [
                ['delete', EventPriority::FIRST],
                ['dispatchDeleted', EventPriority::LAST]
            ]
        ];
    }

    public function __construct(
        private LoggerInterface $logger,
        private EventDispatcherInterface $dispatcher
    ) {
    }

    public function create(<?= $create_event ?> $event): void {
        $this->dispatcher->dispatch(new CreateEntityEvent($event->getEntity(), CreateEntityEvent::getName()));
    }

    public function dispatchCreated(<?= $create_event ?> $event): void {
        $this->dispatcher->dispatch(new <?= $created_event ?>($event->getEntity()))
    }

    public function update(<?= $update_event ?> $event): void {
        $this->dispatcher->dispatch(new UpdateEntityEvent($event->getEntity(), UpdateEntityEvent::getName()));
    }

    public function dispatchUpdated(<?= $update_event ?> $event): void {
        $this->dispatcher->dispatch(new <?= $updated_event ?>($event->getEntity()))
    }

    public function delete(<?= $delete_event ?> $event): void {
        $this->dispatcher->dispatch(new DeleteEntityEvent($event->getEntity(), DeleteEntityEvent::getName()));
    }

    public function dispatchDeleted(<?= $delete_event ?> $event): void {
        $this->dispatcher->dispatch(new <?= $deleted_event ?>($event->getEntity()))
    }
}