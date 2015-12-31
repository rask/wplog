<?php

namespace Wplog\Events\Auth;

use Wplog\Events\Event;

/**
 * Class AuthEvent
 *
 * @since 0.1.0
 * @package Wplog\Events\Auth
 */
class AuthEvent extends Event
{
    /**
     * The type of this event.
     *
     * @since 0.1.0
     * @access protected
     * @var String
     */
    protected $type = 'auth';
}
