<?php

namespace Wplog\Events\Core;

use Wplog\Events\Event;

/**
 * Class CoreEvent
 *
 * @since 0.1.0
 * @package Wplog\Events\Core
 */
class CoreEvent extends Event
{
    /**
     * The type of this event.
     *
     * @since 0.1.0
     * @access protected
     * @var String
     */
    protected $type = 'core';
}
