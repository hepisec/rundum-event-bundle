<?php

namespace Rundum\EventBundle\Enum;

enum EventPriority: int
{
    case FIRST = \PHP_INT_MAX;
    case DEFAULT = 0;
    case LAST = \PHP_INT_MIN;
}