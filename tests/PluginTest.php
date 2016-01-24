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
}
