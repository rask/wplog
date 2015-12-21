<?php

namespace Wplog\Database;

/**
 * Class Database
 *
 * @since 0.1.0
 * @package Wplog\Database
 */
class Database
{
    /**
     * Installed database version found in wp_options.
     *
     * @since 0.1.0
     * @access protected
     * @var Integer
     */
    protected $installedVersion;

    /**
     * Database version defined for the installed plugin.
     *
     * @since 0.1.0
     * @access protected
     * @var Integer
     */
    protected $pluginVersion;

    /**
     * Database constructor.
     *
     * @since 0.1.0
     * @return void
     */
    public function __construct()
    {
        $this->installedVersion = get_option('wplog_database_version', null);
        $this->pluginVersion = WPLOG_DB_VERSION;
    }

    /**
     * Does the database schema need to be installed.
     *
     * @since 0.1.0
     * @return Boolean
     */
    public function needsToInstallDatabase() : bool
    {
        return $this->installedVersion === null;
    }

    /**
     * Does the plugin need to update the database schema?
     *
     * @since 0.1.0
     * @return Boolean
     */
    public function needsToUpdateDatabase() : bool
    {
        if ($this->needsToInstallDatabase()) {
            return true;
        }

        if (!is_numeric($this->installedVersion)) {
            throw new \UnexpectedValueException('Invalid numeric version installed for plugin database.');
        }

        return (int) $this->installedVersion < (int) $this->pluginVersion;
    }

    /**
     * See whether database table updates are needed and update if needed.
     *
     * @since 0.1.0
     * @return Boolean
     */
    public function maybeInstallOrUpdateTables() : bool
    {
        $success = false;

        if ($this->needsToUpdateDatabase()) {
            $success = $this->updateTables();
        }

        return $success;
    }

    /**
     * Create or update the database tables for the plugin.
     *
     * @since 0.1.0
     * @return Boolean
     */
    public function updateTables() : bool
    {
        $update = new TableUpdate($this->pluginVersion);

        return $update->run();
    }
}
