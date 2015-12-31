<?php

namespace Wplog\Tests;

use Wplog\Database\Database;

/**
 * Class DatabaseTest
 *
 * @package Wplog\Tests
 */
class DatabaseTest extends WplogTestCase
{
    function testItInstallsDatabaseTables()
    {
        global $wpdb;

        $wpldb = new Database();

        delete_option('wplog_database_version');

        $wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'wplog');

        $wpldb->updateTables();

        $tableExists = $wpdb->get_var('show tables like "' . $wpdb->prefix . 'wplog"');

        $this->assertTrue((bool) $tableExists);
    }

    function testItValidatesNeedToInstall()
    {
        delete_option('wplog_database_version');

        $wpldb = new Database();

        $this->assertTrue($wpldb->needsToInstallDatabase());
    }

    function testItValidatesNeedToUpdate()
    {
        update_option('wplog_database_version', 0);

        $wpldb = new Database();

        $this->assertTrue($wpldb->needsToUpdateDatabase());
    }
}
