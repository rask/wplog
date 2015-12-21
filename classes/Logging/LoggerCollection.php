<?php

namespace Wplog\Logging;

use Psr\Log\LogLevel;
use Wplog\Events\Event;

/**
 * Class LoggerCollection
 *
 * A collection of loggers, either log to all interfaces or just a few.
 *
 * @since 0.1.0
 * @package Wplog\Logging
 */
class LoggerCollection
{
    /**
     * PSR log levels that can be used for logging.
     *
     * @since 0.1.0
     * @access protected
     * @var String[]
     */
    protected $levels = [
        LogLevel::EMERGENCY,
        LogLevel::CRITICAL,
        LogLevel::ALERT,
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG
    ];
    /**
     * Loggers this collection uses.
     *
     * @since 0.1.0
     * @access protected
     * @var \Wplog\Logging\LogAdapter[]
     */
    protected $loggers = [];

    /**
     * Add a logger to the collection.
     *
     * @since 0.1.0
     *
     * @param \Wplog\Logging\LogAdapter $logger
     *
     * @return void
     */
    public function addLogger(LogAdapter $logger)
    {
        $this->loggers[] = $logger;
    }

    /**
     * Write the event to the logs defined for this collection.
     *
     * @since 0.1.0
     *
     * @param \Wplog\Events\Event $event
     *
     * @return void
     */
    public function write(Event $event)
    {
        foreach ($this->loggers as $logger) {
            $logger->write($event);
        }
    }
}
