<?php

namespace Wplog\Tests;

use Wplog\Plugin;

/**
 * Class PluginTest
 *
 * @package Wplog\Tests
 */
class PluginTest extends WplogTestCase
{
    public $pluginId = 'wplog/wplog.php';

    function testItCanBeInstantiated()
    {
        $pluginInstance = new Plugin();
    }

    function testItCanBeActivatedAndDeactivated()
    {
        $pid = $this->pluginId;

        include_once(getWpRootDir() . '/wp-admin/includes/plugin.php');

        if (is_plugin_active($pid)) {
            deactivate_plugins($pid);
            $this->assertFalse(is_plugin_active($pid));
            activate_plugin($pid);
            $this->assertTrue(is_plugin_active($pid));
        } else {
            activate_plugin($pid);
            deactivate_plugins($pid);
            $this->assertFalse(is_plugin_active($pid));
            activate_plugin($pid);
            $this->assertTrue(is_plugin_active($pid));
        }
    }

    function testItValidatesPluginInstallAndUpdateNeed()
    {
        $plugin = new Plugin();

        delete_option('wplog_plugin_version');

        $needsInstall = $plugin->needsToInstall();
        $needsUpdate = $plugin->needsToUpdate();

        $this->assertTrue($needsInstall, 'Needs to install, case 1');
        $this->assertFalse($needsUpdate, 'Needs to update, case 1');

        update_option('wplog_plugin_version', WPLOG_VERSION);

        $needsInstall = $plugin->needsToInstall();
        $needsUpdate = $plugin->needsToUpdate();

        $this->assertFalse($needsInstall, 'Needs to install, case 2');
        $this->assertFalse($needsUpdate, 'Needs to update, case 2');

        update_option('wplog_plugin_version', '0.0.0');

        $needsInstall = $plugin->needsToInstall();
        $needsUpdate = $plugin->needsToUpdate();

        $this->assertFalse($needsInstall, 'Needs to install, case 3');
        $this->assertTrue($needsUpdate, 'Needs to update, case 3');
    }
}
