<?php

namespace Wplog;

use Wplog\Events\{Options, Posts};

return [

    /**
     * Options.
     */
    'updated_option' => Options\OptionListener::class,

    /**
     * Posts, pages and CPTs.
     */
    'wp_insert_post' => Posts\PostListener::class
];
