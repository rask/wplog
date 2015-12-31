<?php

namespace Wplog\Logging;

use Psr\Log\LoggerInterface;
use Wplog\Events\Event;

/**
 * Class LogAdapter
 *
 * @since 0.1.0
 * @package Wplog\Logging
 */
abstract class LogAdapter implements LoggerInterface
{
    /**
     * Write the event to a log system.
     *
     * @since 0.1.0
     *
     * @param \Wplog\Events\Event $event Event to write.
     *
     * @return Boolean
     */
    public function write(Event $event) : bool
    {
        return $this->log(
            $event->getSeverity(),
            $event->getBody(),
            $event->getContext(),
            $event
        );
    }

    /**
     * System is unusable.
     *
     * @since 0.1.0
     *
     * @param string $message
     * @param array $context
     * @param \Wplog\Events\Event The event being logged.
     *
     * @return void
     */
    public function emergency($message, array $context = array(), Event $event = null)
    {
        $this->log('emergency', $message, $context, $event);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @since 0.1.0
     *
     * @param string $message
     * @param array $context
     * @param \Wplog\Events\Event The event being logged.
     *
     * @return void
     */
    public function alert($message, array $context = array(), Event $event = null)
    {
        $this->log('alert', $message, $context, $event);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @since 0.1.0
     *
     * @param string $message
     * @param array $context
     * @param \Wplog\Events\Event The event being logged.
     *
     * @return void
     */
    public function critical($message, array $context = array(), Event $event = null)
    {
        $this->log('critical', $message, $context, $event);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @since 0.1.0
     *
     * @param string $message
     * @param array $context
     * @param \Wplog\Events\Event The event being logged.
     *
     * @return void
     */
    public function error($message, array $context = array(), Event $event = null)
    {
        $this->log('error', $message, $context, $event);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @since 0.1.0
     *
     * @param string $message
     * @param array $context
     * @param \Wplog\Events\Event The event being logged.
     *
     * @return void
     */
    public function warning($message, array $context = array(), Event $event = null)
    {
        $this->log('warning', $message, $context, $event);
    }

    /**
     * Normal but significant events.
     *
     * @since 0.1.0
     *
     * @param string $message
     * @param array $context
     * @param \Wplog\Events\Event The event being logged.
     *
     * @return void
     */
    public function notice($message, array $context = array(), Event $event = null)
    {
        $this->log('notice', $message, $context, $event);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @since 0.1.0
     *
     * @param string $message
     * @param array $context
     * @param \Wplog\Events\Event The event being logged.
     *
     * @return void
     */
    public function info($message, array $context = array(), Event $event = null)
    {
        $this->log('info', $message, $context, $event);
    }

    /**
     * Detailed debug information.
     *
     * @since 0.1.0
     *
     * @param string $message
     * @param array $context
     * @param \Wplog\Events\Event The event being logged.
     *
     * @return void
     */
    public function debug($message, array $context = array(), Event $event = null)
    {
        $this->log('debug', $message, $context, $event);
    }

    /**
     * Catch-all log method.
     *
     * @param String $level Log level (PSR).
     * @param String $message Log message.
     * @param mixed[] $context Log message context.
     * @param \Wplog\Events\Event|null $event Event we're currently logging.
     *
     * @return Boolean
     */
    abstract public function log($level, $message, array $context = [], Event $event = null) : bool;

    /**
     * Parse message context.
     *
     * @since 0.1.0
     * @access protected
     *
     * @param String $message
     * @param mixed[] $context
     *
     * @return String
     */
    protected function parseContext(string $message, array $context = []) : string
    {
        if (empty($context)) {
            return $message;
        }

        foreach ($context as $key => $val) {
            $key = '{' . $key . '}';
            $message = str_replace($key, strval($val), $message);
        }

        return $message;
    }
}
