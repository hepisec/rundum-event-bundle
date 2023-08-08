<?php

namespace Rundum\EventBundle\Event;

class EntityUpdatedEvent extends AbstractEntityEvent
{
    public static function getName(): string
    {
        return 'entity.updated';
    }
}