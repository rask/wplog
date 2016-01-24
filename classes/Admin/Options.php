<?php

namespace Wplog\Admin;

/**
 * Class Options
 *
 * @since 0.1.0
 * @package Wplog\Admin
 */
class Options
{
    /**
     * Hook to admin menu to generate options page menu item.
     *
     * @since 0.1.0
     * @return void
     */
    public function adminMenu()
    {
        $optionsMenuCapability = apply_filters('wplog/admin/options_capability', 'manage_options');

        // noop until we have got actual options to manage.

        return;

        add_options_page(
            __('Logging Options', 'wplog'),
            __('Logging', 'wplog'),
            $optionsMenuCapability,
            'wplog-options',
            [$this, 'generateOptionsPage']
        );
    }

    /**
     * Generate the options page.
     *
     * @since 0.1.0
     * @return void
     */
    public function generateOptionsPage()
    {
        include(WPLOG_DIR . '/includes/admin/options-page.php');
    }
}
