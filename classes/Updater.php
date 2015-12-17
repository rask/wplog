<?php

namespace Wplog;

use stdClass;

/**
 * Class Updater
 *
 * This class overrides plugin update checks from wordpress.org and allows updating
 * directly from GitHub releases.
 *
 * Idea copied and adapted from
 * {@link http://code.tutsplus.com/tutorials/distributing-your-plugins-in-github-with-automatic-updates--wp-34817 Tuts+}. Thanks!
 *
 * @package Wplog
 */
class Updater
{
    /**
     * Plugin WP identifier, e.g. `plugin/plugin.php`
     *
     * @since 0.1.0
     * @access protected
     * @var String
     */
    protected $pluginId;

    /**
     * Absolute path to the plugin main file.
     *
     * @since 0.1.0
     * @access protected
     * @var string
     */
    protected $pluginFilePath;

    /**
     * GitHub repository ID. E.g. `vendor/repo`.
     *
     * @since 0.1.0
     * @access protected
     * @var String
     */
    protected $githubRepoId;

    /**
     * Current plugin data.
     *
     * @since 0.1.0
     * @access protected
     * @var mixed[]
     */
    protected $pluginData;

    /**
     * Response data from the GitHub releases system.
     *
     * @since 0.1.0
     * @access protected
     * @var \stdClass
     */
    protected $githubResponseData = null;

    /**
     * Was this plugin activated before the update procedure.
     *
     * @since 0.1.0
     * @access protected
     * @var Boolean
     */
    protected $pluginWasActive = false;

    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param String $pluginId identifier, e.g. `plugin/plugin.php`.
     * @param String $githubRepoId The GitHub repository where the plugin upstream is
     *                             located at. E.g. `vendor/repo`.
     *
     * @return void
     */
    public function __construct($pluginId, $githubRepoId)
    {
        // If invalid info do nothing.
        if ($pluginId === null || $githubRepoId === null) {
            return;
        }

        $this->pluginId = $pluginId;
        $this->githubRepoId = $githubRepoId;

        $this->pluginBasename = basename($this->pluginId, '.php');

        $this->pluginFilePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'wplog.php';

        add_filter('pre_set_site_transient_update_plugins', function ($transient) {
            return $this->filterPluginUpdatesTransient($transient);
        }, 25);

        add_filter('plugins_api', function ($false, $action, $response) {
            return $this->filterPluginsApi($false, $action, $response);
        }, 10, 3);

        add_action('upgrader_pre_install', function ($true, $args) {
            $this->upgraderPreInstall($true, $args);
        }, 10, 2);

        add_action('upgrader_post_install', function ($true, $hook_extra, $result) {
            $this->upgraderPostInstall($true, $hook_extra, $result);
        }, 10, 3);
    }

    /**
     * Filter the plugin updates transient which validates whether a plugin has any
     * newer versions available at the plugin update endpoint (wp.org or similar).
     *
     * @access protected
     * @since 0.1.0
     *
     * @param mixed[] $pluginsTransient The transient used to define plugins that are
     *                                  up to date and which need updates.
     *
     * @return mixed
     */
    protected function filterPluginUpdatesTransient($transient)
    {
        // Populate needed data.
        $this->getCurrentPluginData();

        try {
            $this->getGithubReleaseData();
        } catch (\Exception $e) {
            // FIXME log error
            return $transient;
        }

        $outdated = $this->validateNewerVersionAvailable();

        // Up to date, continue checking other plugins.
        if (!$outdated) {
            return $transient;
        }

        // Simulate an API object wordpress.org could return for a plugin that has updates available.
        $pluginUpdateData = $this->getUpdatesTransientInfoObject();

        $transient->response[$this->pluginId] = $pluginUpdateData;

        return $transient;
    }

    /**
     * Filter the plugin data that is fetched to display update information.
     *
     * @access protected
     * @since 0.1.0
     *
     * @param mixed $false What the hell... Return this in case something goes wrong.
     * @param mixed $action Current action perhaps?
     * @param Object $response The API response struct.
     *
     * @return mixed
     */
    protected function filterPluginsApi($false, $action, $response)
    {
        if (!isset($response->slug) || $response->slug !== $this->pluginBasename) {
            return $false;
        }

        if ($action !== 'plugin_information') {
            return $false;
        }

        // Fetch data again just in case.
        $this->getCurrentPluginData();
        $this->getGithubReleaseData();

        $response->last_updated = $this->githubResponseData->published_at;
        $response->slug = $this->pluginId;
        $response->plugin_name = $this->pluginData['Name'];
        $response->version = $this->getGithubVersion();
        $response->author = $this->pluginData['Author'];
        $response->homepage = $this->pluginData['PluginURI'];
        $response->download_link = $this->getGithubReleaseZipUrl();

        $response->sections = $this->getPluginUpdateSections();

        return $response;
    }

    /**
     * Run logic before the plugin updates are installed.
     *
     * @access protected
     * @since 0.1.0
     *
     * @param mixed $true What the hell...
     * @param mixed $args Hook arguments.
     *
     * @return void
     */
    protected function upgraderPreInstall($true, $args)
    {
        // Save active data before installation.
        $this->pluginWasActive = is_plugin_active($this->pluginId);

        return;
    }

    /**
     * Run logic after updating the plugin.
     *
     * As GitHub releases are named `repo-x.y.z.zip`, the plugin will be installed to
     * a new directory with that exact name. We need to move the new contents to the
     * real plugin directory after installation and then remove the old directory.
     *
     * @access protected
     * @since 0.1.0
     * @global mixed $wp_filesystem Filesystem to make changes.
     *
     * @param mixed $true What the hell...
     * @param mixed $hook_extra Extra args.
     * @param mixed $result Update result?
     *
     * @return void
     */
    protected function upgraderPostInstall($true, $hook_extra, $result)
    {
        global $wp_filesystem;

        // Load it again just in case.
        $this->getCurrentPluginData();

        $pluginDirectory = dirname($this->pluginFilePath);

        $wp_filesystem->move($result['destination'], $pluginDirectory);

        $result['destination'] = $pluginDirectory;

        // Activate the plugin if it was activated before the upgrade.
        if ($this->pluginWasActive) {
            activate_plugin($this->pluginId);
        }

        return;
    }

    /**
     * Get the currently installed plugin data for wplog.
     *
     * @access protected
     * @since 0.1.0
     * @return void
     */
    protected function getCurrentPluginData()
    {
        $this->pluginData = get_plugin_data($this->pluginFilePath);
    }

    /**
     * Get the latest release data from GitHub for this plugin.
     *
     * @access protected
     * @since 0.1.0
     * @return void
     */
    protected function getGithubReleaseData()
    {
        // Prevent running the fetch multiple times in a row.
        if ($this->githubResponseData !== null) {
            return;
        }

        // Releases endpoint.
        $releasesUrl = sprintf('https://api.github.com/repos/%s/releases', $this->githubRepoId);

        // Fetch the data.
        $response = wp_remote_get($releasesUrl);
        $responseStatus = wp_remote_retrieve_response_code($response);
        $responseBody = wp_remote_retrieve_body($response);

        // No body or the request fetched an error response.
        if (empty($responseBody) || (int) $responseStatus !== 200 || stripos($responseBody, 'tag_name') === false) {
            throw new \UnexpectedValueException('Could not fetch release data from GitHub. Response was: ' . $responseBody);
        }

        $responseData = json_decode($responseBody);

        // Pick the latest only.
        if (is_array($responseData)) {
            $responseData = array_shift($responseData);
        }

        /**
         * Allow filtering the data that comes from GitHub for this plugin update.
         *
         * @since 0.1.0
         *
         * @param mixed[] $responseData The GitHub response data.
         */
        $responseData = apply_filters('wplog/update/github_response_data', $responseData);

        $this->githubResponseData = $responseData;
    }

    /**
     * Validate the local and the upstream versions.
     *
     * @access protected
     * @since 0.1.0
     * @see self::filterPluginUpdatesTransient()
     * @return Boolean
     */
    protected function validateNewerVersionAvailable()
    {
        $localVersion = $this->pluginData['Version'];
        $upstreamVersion = $this->getGithubVersion();

        // Return true if the GitHub tag version is higher.
        $newerAvailable = version_compare($upstreamVersion, $localVersion, '>');

        return $newerAvailable ? true : false;
    }

    /**
     * Build an update status object that emulates the data that wordpress.org would
     * return for a plugin update check.
     *
     * @access protected
     * @since 0.1.0
     * @return \stdClass
     */
    protected function getUpdatesTransientInfoObject()
    {
        $object = new stdClass();

        // Release package download URL.
        //FIXME validate where custom upload binaries go in the releases system
        $packageUrl = $this->getGithubReleaseZipUrl();

        $object->id = '0';
        $object->slug = $this->pluginBasename;
        $object->plugin = $this->pluginId;
        $object->new_version = $this->getGithubVersion();
        $object->url = $this->pluginData['PluginURI'];
        $object->package = $packageUrl;

        return $object;
    }

    /**
     * Get the URL to the zip file for a GitHub release.
     *
     * @access protected
     * @since 0.1.0
     * @return String
     */
    protected function getGithubReleaseZipUrl()
    {
        $releaseAssets = $this->githubResponseData->assets;

        if (empty($releaseAssets)) {
            throw new \Exception('Cannot update wplog, no release asset (zip) available for the release.');
        }

        $releaseZip = array_shift($releaseAssets);

        return $releaseZip->browser_download_url;
    }

    /**
     * Get the text sections for the plugin update modal etc.
     *
     * @access protected
     * @since 0.1.0
     * @return String[]
     */
    protected function getPluginUpdateSections()
    {
        $sections = [];

        $description = $this->pluginData['Description'];

        $changes = $this->githubResponseData->body;
        $changes = \Parsedown::instance()->parse($changes);

        return [
            'description' => $description,
            'changes' => $changes
        ];
    }

    /**
     * Get the version number for the latest GitHub release.
     *
     * @access protected
     * @since 0.1.0
     * @return mixed
     */
    protected function getGithubVersion()
    {
        return $this->githubResponseData->tag_name;
    }
}
