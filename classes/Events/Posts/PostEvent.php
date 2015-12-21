<?php

namespace Wplog\Events\Posts;

use Wplog\Events\Event;

/**
 * Class PostEvent
 *
 * @since 0.1.0
 * @package Wplog\Events
 */
class PostEvent extends Event
{
    /**
     * The type of this event.
     *
     * @since 0.1.0
     * @access protected
     * @var String
     */
    protected $type = 'post';
}
