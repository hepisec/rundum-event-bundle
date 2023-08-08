<?php

namespace Rundum\EventBundle\Event;

use Rundum\EventBundle\Contracts\EntityEventInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractEntityEvent extends Event implements EntityEventInterface
{
    public function __construct(
        private mixed $entity
    )
    {       
    }

    public function getEntity(): mixed
    {
        return $this->entity;
    }
}