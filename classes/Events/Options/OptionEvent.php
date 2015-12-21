<?php

namespace Wplog\Events\Options;

use Wplog\Events\Event;

/**
 * Class OptionEvent
 *
 * Used to log wp_options related changes.
 *
 * @since 0.1.0
 * @package Wplog\Events
 */
class OptionEvent extends Event
{
    /**
     * Type of event.
     *
     * @since 0.1.0
     * @access protected
     * @var string
     */
    protected $type = 'option';
}
