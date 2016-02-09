<?php

namespace Wplog\Logging\File;

use Wplog\Events\Event;
use Wplog\Logging\LogAdapter;

/**
 * Class Logger
 *
 * @since 0.1.0
 * @package Wplog\Logging\File
 */
class Logger extends LogAdapter
{
    /**
     * Log file to output entries to.
     *
     * @since 0.1.0
     * @access protected
     * @var String
     */
    protected $logFile;

    /**
     * Get the log file this logger is using.
     *
     * @since 0.1.0
     * @return String
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * Set the log file this logger should use.
     *
     * @since 0.1.0
     * @param String $logFile
     */
    public function setLogFile($logFile)
    {
        $this->logFile = $logFile;
    }

    /**
     * Catch-all log method.
     *
     * @fixme Validate log rotation needs and log size limits.
     *
     * @since 0.1.0
     *
     * @param String $level Log level (PSR).
     * @param String $message Log message.
     * @param mixed[] $context Log message context.
     * @param \Wplog\Events\Event|null $event Event we're currently logging.
     *
     * @return Boolean
     */
    public function log($level, $message, array $context = [], Event $event = null) : bool
    {
        try {
            if ($event === null) {
                throw new \InvalidArgumentException('No event given for file logger logging method.');
            }

            file_put_contents($this->logFile, (string) $event . "\n", FILE_APPEND);

            return true;
        } catch (\Exception $e) {
            \Wplog\wplog()->internalLogger()->error('Could not log using file logger: ' . $e->getMessage());

            return false;
        }
    }
}
