<?php

namespace Wplog\Logging;

use Psr\Log\LoggerInterface;

/**
 * Class InternalLogger
 *
 * This class is used for plugin specific internal debug and error logging.
 *
 * @since 0.1.0
 * @package Wplog\Logging
 */
class InternalLogger implements LoggerInterface
{
    /**
     * Log file path
     *
     * @since 0.1.0
     * @access protected
     * @var string
     */
    protected $logFile;

    /**
     * Maximum filesize in bytes for log file.
     *
     * @since 0.1.0
     * @access protected
     * @var integer
     */
    protected $logFileMaxSize;

    /**
     * InternalLogger constructor.
     *
     * @since 0.1.0
     * @return void
     */
    public function __construct()
    {
        $this->logFile = wp_upload_dir()['path'] . '/wplog.internal.log';
        $this->logFileMaxSize = 1024 * 1024;
    }

    /**
     * System is unusable.
     *
     * @since 0.1.0
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function emergency($message, array $context = array())
    {
         $this->log('emergency', $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * @since 0.1.0
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function alert($message, array $context = array())
    {
        $this->log('alert', $message, $context);
    }

    /**
     * Critical conditions.
     *
     * @since 0.1.0
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function critical($message, array $context = array())
    {
        $this->log('critical', $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @since 0.1.0
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function error($message, array $context = array())
    {
        $this->log('error', $message, $context);
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
     *
     * @return void
     */
    public function warning($message, array $context = array())
    {
        $this->log('warning', $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @since 0.1.0
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function notice($message, array $context = array())
    {
        $this->log('notice', $message, $context);
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
     *
     * @return void
     */
    public function info($message, array $context = array())
    {
        $this->log('info', $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @since 0.1.0
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function debug($message, array $context = array())
    {
        $this->log('debug', $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @since 0.1.0
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        // Parse context for log message.
        if (!empty($context)) {
            foreach ($context as $k => $v) {
                $message = str_replace('{' . $k . '}', $v, $message);
            }
        }

        $now = new \DateTime('now');
        $timestamp = $now->format('Y-m-d H:i:s');

        // timestamp severity message
        $logLine = sprintf(
            '%s %s %s',
            $timestamp,
            $level,
            $message
        );

        // Respect max file size for log file.
        if (filesize($this->logFile) > $this->logFileMaxSize) {
            file_put_contents($this->logFile, '');
        }

        // Log the message!
        file_put_contents($this->logFile, $logLine . "\n", FILE_APPEND);
    }

    /**
     * Get the log file that is used for logging.
     *
     * @since 0.1.0
     * @return string
     */
    public function getLogFile() : string
    {
        return $this->logFile;
    }

    /**
     * Set the log file that is used for logging.
     *
     * @since 0.1.0
     * @param string $logFile
     */
    public function setLogFile(string $logFile)
    {
        $this->logFile = $logFile;
    }
}
