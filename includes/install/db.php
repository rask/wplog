<?php

namespace Wplog;

global $wpdb;

if (!isset($dbVer)) {
    exit;
}

$schema = null;
$collation = $wpdb->get_charset_collate();

switch ($dbVer) {

    case 1:

        $schema = <<<SQLSCHEMA
CREATE TABLE {$wpdb->prefix}wplog (
    uuid binary(16) NOT NULL,
    timestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    severity varchar(32) NOT NULL,
    event_type varchar(32) NOT NULL,
    body varchar(2048) NOT NULL,
    user_id bigint(9),
    UNIQUE KEY uuid (uuid)
) {$collation};
SQLSCHEMA;

        break;

}

return $schema;
