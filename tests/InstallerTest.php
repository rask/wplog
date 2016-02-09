<?php

namespace Wplog\Tests;

use Wplog\Installer;

/**
 * Class InstallerTest
 *
 * @package Wplog\Tests
 */
class InstallerTest extends WplogTestCase
{
    function testItInstallsPlugin()
    {
        delete_option('wplog_plugin_version');
        delete_option('wplog_options');

        $installer = new Installer();

        $installer->maybeInstallOrUpdatePlugin();

        $ver = get_option('wplog_plugin_version', null);
        $opt = get_option('wplog_options', null);

        $this->assertNotNull($ver);
        $this->assertNotNull($opt);
    }
}
