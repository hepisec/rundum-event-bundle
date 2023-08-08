<?php

namespace Rundum\EventBundle\Event;

class EntityDeletedEvent extends AbstractEntityEvent
{
    public static function getName(): string
    {
        return 'entity.deleted';
    }
}