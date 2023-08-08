<?php

namespace Rundum\EventBundle\Event;

use Rundum\EventBundle\Contracts\DeleteEventInterface;

class DeleteEntityEvent extends AbstractEntityEvent implements DeleteEventInterface
{
    public static function getName(): string
    {
        return 'entity.delete';
    }
}