# wplog

*wplog* is a system logging plugin for WordPress.

## Disclaimer

Currently the plugin is in early development. Bugs may be frequent and things might
break down badly if used. Use this plugin in production *only* if you're ready
encounter bugs and missing features.

## Features

### Log system events

Logs all kinds of changes in the WordPress system:

-   Option value changes
-   Authentication
-   Posts (including custom post types and meta changes)

More to come.

### Log to various log endpoints

*wplog* comes bundled with a `$wpdb` logger, which keeps logs in a custom database
table.

Third party log endpoints are quite easy to create, and only defining a class, a
logging method and registering the logger to *wplog* is needed to make it work (more
documentation on this later when the API is finished).

### Custom log events

Custom log events can be created to keep log of your customized WP installations
(more documentation on this when the API is finished).

## Installation

During development phase: clone this repo to your `wp-plugins` directory and run the
following inside it

    $ composer install && npm install
    $ gulp

and then activate in `wp-admin`. By default the plugin will use the `$wpdb` logger.

### Requirements

The plugin requires a PHP **7** system. This means WP 4.4 and upwards is supported.

## Updates

The plugin features code which should update it directly from GitHub releases. If you
encounter problems during updates, please create an issue so we can take a look.

## Uninstallation

Remove the plugin inside `wp-admin`. If you want to just "delete" it, remove the
plugin directory and dispose of the `{$prefix}_wplog` database table.

### Logged data

If you've used custom log endpoints, you will need to handle removing the data from
those endpoints yourself if you uninstall this plugin.

If you decide to keep the logged data as is, and reinstall this plugin later, no
conflicts should occur with old and new data.

## TODO

-   Documentation.
-   Plain file logging endpoint to plugin core.
-   A way to actually view the logs generated using the core endpoints.
-   A way to make external logging service logs viewable in wp-admin.
-   Define global and endpoint specific settings which should be customizeable.

## Contributing

-   Create issues at the issue tracker.
-   Send pull requests for fixes and features.

Once the plugin is more mature, we're going to start keeping a list of good logging
endpoints that third parties have created.

## License

GPLv3+, see `LICENSE.md`.
