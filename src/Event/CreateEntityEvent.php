<?php

namespace Rundum\EventBundle\Event;

use Rundum\EventBundle\Contracts\CreateEventInterface;

class CreateEntityEvent extends AbstractEntityEvent implements CreateEventInterface
{
    public static function getName(): string
    {
        return 'entity.create';
    }
}