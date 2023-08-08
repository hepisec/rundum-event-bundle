<?php

namespace Rundum\EventBundle\Event;

class EntityCreatedEvent extends AbstractEntityEvent
{
    public static function getName(): string
    {
        return 'entity.created';
    }
}