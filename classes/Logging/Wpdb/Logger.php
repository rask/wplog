<?php

namespace Wplog\Logging\Wpdb;

use Wplog\Events\Event;
use Wplog\Logging\LogAdapter;
use Wplog\Support\Uuid;

/**
 * Class Logger
 *
 * @since 0.1.0
 * @package Wplog\Logging\Wpdb
 */
class Logger extends LogAdapter
{
    /**
     * $wpdb instance mapped for this logger.
     *
     * @var \wpdb;
     */
    protected $wpdb;

    /**
     * WpdbLogger constructor.
     *
     * Map WPDB to self.
     *
     * @since 0.1.0
     * @return void
     */
    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @since 0.1.0
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @param \Wplog\Events\Event The event being logged.
     *
     * @return Boolean
     */
    public function log($level, $message, array $context = array(), Event $event = null) : bool
    {
        $message = $this->parseContext($message, $context);

        $inserted = $this->wpdb->insert(
            $this->wpdb->prefix . 'wplog',
            [
                'uuid' => Uuid::pack($event->getUuid()),
                'timestamp' => $event->getTimestamp(),
                'severity' => $level,
                'event_type' => $event->getType(),
                'body' => $message,
                'user_id' => $event->getUserId()
            ],
            [
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d'
            ]
        );

        return $inserted === 1;
    }
}
