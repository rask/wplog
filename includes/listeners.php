<?php

namespace Wplog;

use Wplog\Events\{Options, Posts, Auth, Core};

return [

    /**
     * Options.
     */
    'updated_option' => Options\OptionListener::class,

    /**
     * Posts, pages and CPTs.
     */
    'wp_insert_post' => Posts\PostListener::class,

    /**
     * Post meta, post type independent.
     */
    'updated_post_meta' => Posts\PostMetaListener::class,

    /**
     * User login/logout.
     */
    'wp_login' => Auth\AuthListener::class,
    'clear_auth_cookie' => Auth\AuthListener::class,

    /**
     * Core upgrades.
     */
    'upgrader_process_complete' => Core\CoreListener::class,

];
