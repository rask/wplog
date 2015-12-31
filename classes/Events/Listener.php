<?php

namespace Wplog\Events;

/**
 * Class Listener
 *
 * A listener is an object which is used to listen for WP hook triggers and then
 * generate a log event for the hook to pass to the logging system.
 *
 * A listener must define a Event class to use, and possible WP hook handling
 * methods to use. The `onHookTrigger` is built to call handling methods as follows:
 *
 *     1. WP hook `save_post` is triggered.
 *     2. Listener takes in the triggered hook name and arguments it was triggered
 *        with.
 *     3. The `onHookTrigger` method takes the hook name and generates a method name
 *        `save_post` -> `onSavePost`.
 *     4. If a Listener method called `onSavePost` exists, call it and return the
 *        result (hopefully an Event object).
 *
 * The `onHookTrigger` can be overridden in subclasses to allow more custom hook
 * handling, in case hook names are something else than the conventional `snake_case`
 * WordPress style.
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
     * Fired when a WP hook is triggered. Will proceed the triggered call to some
     * other method which handles creating the loggable event.
     *
     * @since 0.1.0
     *
     * @param String $hook The hook which triggered this listener method.
     * @param mixed[] $args Arguments from the fired WP hook.
     *
     * @return \Wplog\Events\Event
     */
    public function onHookTrigger($hook, array $args) : Event
    {
        // Generate a callable method name from hook.
        $methodName = 'on' . implode(array_map('ucfirst', explode('_', $hook)));

        if (method_exists($this, $methodName)) {
            return $this->$methodName($args);
        }

        throw new \BadMethodCallException('Could not log event for WP hook `' . $hook . '`: listener ' . __CLASS__ . ' does not define handling method.');
    }

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
