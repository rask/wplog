<?php

namespace Wplog\Events;
use Wplog\Logging\LoggerCollection;
use Wprsrv\Logger;

/**
 * Class Handler
 *
 * Registers log events for WP hooks and tells a logger collection to write the
 * events this handler has registered.
 *
 * @since 0.1.0
 * @package Wplog\Events
 */
class Handler
{
    /**
     * Collection of listeners this handler is using.
     *
     * @since 0.1.0
     * @access protected
     * @var \Wplog\Events\Event[]
     */
    protected $listenerEvents = [];

    /**
     * Loggers to use for handled event logging.
     *
     * @since 0.1.0
     * @access protected
     * @var \Wplog\Logging\LoggerCollection
     */
    protected $loggerCollection;

    /**
     * Handler constructor.
     *
     * @since 0.1.0
     *
     * @param \Wplog\Logging\LoggerCollection $loggers
     *
     * @return void
     */
    public function __construct(LoggerCollection $loggerCollection)
    {
        $this->loggerCollection = $loggerCollection;
    }

    /**
     * Add a listener for a WP hook.
     *
     * @since 0.1.0
     *
     * @param string $hook Hook to create listener for.
     * @param \Wplog\Events\Listener $listener The closure that creates the event for
     *                                         the hook.
     *
     * @return void
     */
    public function addListener(string $hook, Listener $listener)
    {
        // Create a callback wrapper to let the world outside the hook know about it.
        $cb = function (...$args) use ($listener) {
            $event = $listener->onHookTrigger(current_filter(), $args);

            if ($event instanceof Event) {
                $this->listenerEvents[] = $event;
            }
        };

        add_action($hook, $cb, 999, 10);
    }

    /**
     * Handle and log the events this handler has listened to.
     *
     * @since 0.1.0
     * @access protected
     * @return void
     */
    public function handleEvents()
    {
        if (empty($this->listenerEvents)) {
            return;
        }

        foreach ($this->listenerEvents as $event) {
            try {
                $this->loggerCollection->write($event);
            } catch (\Throwable $t) {
                //FIXME internal logging
            }
        }
    }
}
