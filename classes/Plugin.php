<?php

namespace Wplog;

use Wplog\Database\Database;
use Wplog\Events\Handler;
use Wplog\Logging\LogAdapter;
use Wplog\Logging\LoggerCollection;
use Wplog\Logging\WpdbLogger;

/**
 * Class Plugin
 *
 * @since 0.1.0
 * @package Wplog
 */
class Plugin
{
    /**
     * Logging event handler.
     *
     * @since 0.1.0
     * @access protected
     * @var \Wplog\Events\Handler
     */
    protected $handler;

    /**
     * Internal logger for plugin internal logging.
     *
     * @since 0.1.0
     * @access protected
     * @var \Wplog\Logging\InternalLogger
     */
    protected $logger = null;

    /**
     * Hook to WP.
     *
     * Sets up database update checks and updates themselves. Then it sets up the
     * event handler system.
     *
     * @since 0.1.0
     * @return void
     */
    public function initialize()
    {
        // Update plugin internals if needed.
        add_action('init', [new Installer(), 'maybeInstallOrUpdatePlugin']);

        // Update database tables if needed.
        add_action('init', [new Database(), 'maybeInstallOrUpdateTables']);

        // Setup the event handler that collects log events using listeners.
        $this->setupEventHandler();

        if (is_admin()) {
            $this->initializeAdmin();
        }
    }

    /**
     * Get the internal logger instance for plugin specific logging purposes.
     *
     * @since 0.1.0
     * @return \Wplog\Logging\InternalLogger
     */
    public function internalLogger() : InternalLogger
    {
        if ($this->logger instanceof InternalLogger) {
            return $this->logger;
        }

        $this->logger = new InternalLogger();

        return $this->logger;
    }

    /**
     * Initialize admin panel side for plugin.
     *
     * @since 0.1.0
     * @return void
     */
    public function initializeAdmin()
    {
        $optionsPage = new Admin\Options();

        add_action('admin_menu', [$optionsPage, 'adminMenu']);
    }

    /**
     * Get a collection of loggers to use for logging events.
     *
     * @since 0.1.0
     * @return \Wplog\Logging\LoggerCollection
     */
    public function getLoggerCollection()
    {
        /**
         * Allow filtering the base loggers which Wplog offers.
         *
         * @since 0.1.0
         *
         * @param \Wplog\Logging\LogAdapter[] $loggers Logger instances.
         *
         * @return \Wplog\Logging\LogAdapter[]
         */
        $loggers = apply_filters('wplog/loggers', [
            new WpdbLogger()
        ]);

        // Rewrap in case someone accidentally forces a single logger through.
        if ($loggers instanceof LogAdapter) {
            trigger_error('Wplog filter `wplog/loggers` is expected to return an array of LogAdapter instances, not a single LogAdapter instance.', E_USER_WARNING);
            $loggers = [$loggers];
        }

        $loggerCollection = new LoggerCollection();

        foreach ($loggers as $logger) {
            $loggerCollection->addLogger($logger);
        }

        return $loggerCollection;
    }

    /**
     * Create event handlers.
     *
     * Creates a new handler using the base logger collection. Additionally registers
     * a PHP shutdown function to handle the actual log writes for the logger
     * collection.
     *
     * Listener definitions are read from a listener config file, and it can be
     * filtered after loading. The handler then adds each listener to itself.
     *
     * @since 0.1.0
     * @access protected
     * @return void
     */
    protected function setupEventHandler()
    {
        $this->handler = new Handler($this->getLoggerCollection());

        // Do actual logging after the PHP scripts are done to avoid slowing down execution.
        register_shutdown_function([$this->handler, 'handleEvents']);

        $listeners = include(WPLOG_DIR . '/includes/listeners.php');

        /**
         * Allow filtering the hooks<->event listeners setup for logging.
         *
         * @since 0.1.0
         *
         * @param mixed[] $listeners Key-value pairs of WP hooks and listener class
         *                           names.
         *
         * @return mixed[]
         */
        $listeners = apply_filters('wplog/listeners', $listeners);

        foreach ($listeners as $hook => $listener) {
            $this->handler->addListener($hook, new $listener());
        }
    }
}
