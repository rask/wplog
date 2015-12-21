<?php

namespace Wplog\Events;

/**
 * Class Listener
 *
 * A listener is an object which maps WP hooks to specific log event classes.
 *
 * @since 0.1.0
 * @package Wplog\Events
 */
abstract class Listener
{
    /**
     * Type of event class to use for this listener.
     *
     * @since 0.1.0
     * @access protected
     * @var String
     */
    protected static $eventClass = Event::class;

    /**
     * Fired when a WP hook is triggered. Should generate arguments for a new event.
     *
     * @since 0.1.0
     * @abstract
     *
     * @param String $hook The hook which triggered this listener method.
     * @param mixed[] $args Arguments from the fired WP hook.
     *
     * @return mixed
     */
    abstract public function onHookTrigger($hook, $args) : Event;

    /**
     * Get the current logged in user ID for user reference in logs.
     *
     * @since 0.1.0
     * @access protected
     * @return Integer|null
     */
    protected function getCurrentUserId()
    {
        $user = wp_get_current_user();

        return $user->ID ?? null;
    }

    /**
     * Create an event instance for the listened hook trigger.
     *
     * @since 0.1.0
     *
     * @param mixed $args Arguments to map to the event.
     *
     * @return \Wplog\Events\Event
     */
    protected function createEvent($args) : Event
    {
        /** @var \Wplog\Events\Event $event */
        $event = new static::$eventClass();

        if (isset($args['timestamp'])) {
            $event->setTimestamp($args['timestamp']);
        }

        $event->setBody($args['body']);
        $event->setContext($args['context']);
        $event->setSeverity($args['severity']);
        $event->setUserId($args['user_id']);

        return $event;
    }
}
