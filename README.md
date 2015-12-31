# wplog

*wplog* is a system logging plugin for WordPress.

## Features

### Log system events

Logs all kinds of changes in the WordPress system:

-   Option value changes
-   Authentication
-   Posts (including custom post types and meta changes)

More to come.

### Log to various log endpoints

*wplog* comes bundled with two log endpoints: WPDB and regular file logging.

Third party log endpoints are quite easy to create, and only defining a class, a
logging method and registering the logger to *wplog* is needed to make it work.

### Custom log events

Custom log events can be created to keep log of your customized WP installations.

## Installation

Download a ZIP, unzip to your `plugins` directory and activate in `wp-admin`. By
default the plugin will use the WPDB logger.

### Requirements

The plugin requires a PHP **7** system. This means WP 4.4 and upwards is supported.

## Uninstallation

Remove the plugin inside `wp-admin`. If you want to just "delete" it, remove the
plugin directory and dispose of the `{$prefix}_wplog` database table.

### Logged data

The plugin removes the core log endpoint data on uninstall. If you've used custom log
endpoints, you will need to handle removing the data from those endpoints yourself.

If you decide to keep the logged data as is, and reinstall this plugin later, no
conflicts should occur with old and new data.

## License

GPLv3+, see `LICENSE.md`.
