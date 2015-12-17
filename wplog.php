<?php

namespace Wplog;

/**
* Plugin name: wplog
* Description: Logging system for WordPress. Mainly for developers and other tinkerers.
* Version: 0.1.0
* Plugin URI:
* Author: Otto J. Rask
* Author URI: https://www.ottorask.com
* License: GPLv3 (see LICENSE.md)
* Text Domain: wplog
* Domain Path: /languages
*/

define('WPLOG_DIR', __DIR__);
define('WPLOG_GITHUB_REPO_ID', null);
define('WPLOG_PLUGIN_DIR_ID', basename(__DIR__) . '/' . basename(__FILE__));

/**
 * Autoloading.
 */
require_once(__DIR__ . '/vendor/autoload.php');

/**
 * Run updater.
 */
if (is_admin()) {
    new \Wplog\Updater(WPLOG_PLUGIN_DIR_ID, WPLOG_GITHUB_REPO_ID);
}
