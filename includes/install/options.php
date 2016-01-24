<?php

namespace Wplog;

use Wplog\Logging\WpdbLogger;

/**
 * options.php
 *
 * Default options for the plugin.
 *
 * @since 0.1.0
 * @package Wplog
 */

return [

    // Logging enabled.
    'logging_enabled' => true,

    // Available and enabled logging endpoints.
    'enabled_logging_endpoints' => [
        WpdbLogger::class
    ],

    // Options for each logging endpoint.
    'endpoint_options' => [
        WpdbLogger::class => [

        ]
    ]

];
