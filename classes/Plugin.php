<?php

namespace Wplog;
use Wplog\Database\Database;
use Wplog\Events\Handler;
use Wplog\Events\OptionListener;
use Wplog\Events\PostListener;
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
     * Database handler for the plugin.
     *
     * @since 0.1.0
     * @access protected
     * @var \Wplog\Database\Database
     */
    protected $database;

    /**
     * Logging event handler.
     *
     * @since 0.1.0
     * @access protected
     * @var \Wplog\Events\Handler
     */
    protected $handler;

    /**
     * Does the plugin need to be installed.
     *
     * @since 0.1.0
     * @return Boolean
     */
    public function needsToInstall() : bool
    {
        $installedVersion = get_option('wplog_plugin_version', null);

        return $installedVersion === null;
    }

    /**
     * Validate whether the plugin needs to update. Run other updates not related to
     * actual WP update framework updates depending on this value.
     *
     * @since 0.1.0
     * @return Boolean
     */
    public function needsToUpdate() : bool
    {
        if ($this->needsToInstall()) {
            return false;
        }

        $installedVersion = get_option('wplog_plugin_version');

        if (!preg_match('%^\d+\.\d+\.\d+$%', $installedVersion)) {
            throw new \UnexpectedValueException('Invalid semver version installed for plugin.');
        }

        return version_compare($installedVersion, WPLOG_VERSION, '<');
    }

    /**
     * Hook to WP.
     *
     * @since 0.1.0
     * @return void
     */
    public function initialize()
    {
        $this->database = new Database();

        // Update database tables if needed.
        add_action('init', [$this->database, 'maybeInstallOrUpdateTables']);

        $this->setupEventHandler();
    }

    /**
     * Get a collection of loggers to use for logging events.
     *
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
