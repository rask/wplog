<?php

namespace Wplog\Database;

/**
 * Class TableUpdate
 *
 * @since 0.1.0
 * @package Wplog\Database
 */
class TableUpdate
{
    /**
     * The database version this update is going to run.
     *
     * @since 0.1.0
     * @access protected
     * @var Integer
     */
    protected $databaseVersion;

    /**
     * TableUpdate constructor.
     *
     * @since 0.1.0
     *
     * @param Integer $dbVer The database version to update table to.
     *
     * @return void
     */
    public function __construct(int $dbVer)
    {
        $this->databaseVersion = $dbVer;
    }

    /**
     * Get the update schema query for a database version.
     *
     * @since 0.1.0
     * @access protected
     * @return String
     */
    protected function getDatabaseUpdateQuery() : string
    {
        // Used inside includes/install/db.php for picking a version schema.
        $dbVer = $this->databaseVersion;

        // Pick a version schema from installation files.
        $schema = include(WPLOG_DIR . '/includes/install/db.php');

        if ($schema === null) {
            throw new \InvalidArgumentException('Invalid database version given for database table updater.');
        }

        return $schema;
    }

    /**
     * Run the table update.
     *
     * @since 0.1.0
     * return Boolean
     */
    public function run() : bool
    {
        // Get dbDelta.
        include_once(ABSPATH . '/wp-admin/includes/upgrade.php');

        $query = $this->getDatabaseUpdateQuery();

        dbDelta($query);

        return true;
    }
}
