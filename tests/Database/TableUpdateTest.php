<?php

namespace Wplog\Tests\Database;

use Wplog\Database\TableUpdate;
use Wplog\Tests\WplogTestCase;

class TableUpdateTest extends WplogTestCase
{
    function testItUpdatesTable()
    {
        global $wpdb;

        $tableUpdate = new TableUpdate(1);

        $tableUpdate->run();

        $tableExists = $wpdb->get_var('show tables like "' . $wpdb->prefix . 'wplog"');

        $this->assertTrue((bool) $tableExists);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    function testItFailsInexistentDbVersionUpdate()
    {
        $tableUpdate = new TableUpdate(999999);

        $tableUpdate->run();
    }
}
