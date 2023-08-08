<?php

namespace Rundum\EventBundle\Contracts;

interface EntityEventInterface
{
    public function getEntity(): mixed;
    public static function getName(): string;
}