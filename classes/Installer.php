<?php

namespace Wplog;

/**
 * Class Installer
 *
 * Installs and updates plugin settings and data which cannot be updated through the
 * WP update system as is.
 *
 * @since 0.1.0
 * @package Wplog
 */
class Installer
{
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
     * Check whether the plugin needs to be installed or updated, then run the
     * procedures if needed.
     *
     * @since 0.1.0
     * @return Boolean
     */
    public function maybeInstallOrUpdatePlugin() : bool
    {
        if ($this->needsToInstall()) {
            return $this->install();
        }

        if ($this->needsToUpdate()) {
            return $this->update();
        }

        return false;
    }

    /**
     * Install the plugin and needed data.
     *
     * @access protected
     * @since 0.1.0
     * @return Boolean
     */
    protected function install() : bool
    {
        delete_option('wplog_options');

        $defaultOptions = include(WPLOG_DIR . '/includes/install/options.php');

        $defaultOptionsSet = add_option('wplog_options', $defaultOptions);
        $versionSet = add_option('wplog_plugin_version', WPLOG_VERSION);

        $success = $defaultOptionsSet && $versionSet;

        return $success;
    }

    /**
     * Update the plugin and needed data.
     *
     * @access protected
     * @since 0.1.0
     * @return Boolean
     */
    protected function update() : bool
    {
        // noop for now

        return true;
    }
}
