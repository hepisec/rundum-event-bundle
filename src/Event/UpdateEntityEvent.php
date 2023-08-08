<?php

namespace Rundum\EventBundle\Event;

use Rundum\EventBundle\Contracts\UpdateEventInterface;

class UpdateEntityEvent extends AbstractEntityEvent implements UpdateEventInterface
{
    public static function getName(): string
    {
        return 'entity.update';
    }
}