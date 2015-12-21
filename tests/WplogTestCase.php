<?php

namespace Wplog\Tests;

/**
 * Class WplogTestCase
 *
 * @package Wplog\Tests
 */
class WplogTestCase extends \PHPUnit_Framework_TestCase
{
    function setUp()
    {
        global $wpdb;

        parent::setUp();

        $wpdb->query('START TRANSACTION;');
    }

    function tearDown()
    {
        global $wpdb;

        parent::tearDown();

        $wpdb->query('ROLLBACK;');
    }
}
