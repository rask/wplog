<?php

function getWpRootDir()
{
    $testRoot = getenv('WP_TESTS_DIR');

    if (!$testRoot) {
        $testRoot = '/tmp/wordpress-tests-lib';
    }

    if (!is_dir($testRoot)) {
        $pathParts = explode('wp-content', __DIR__);
        $testRoot = array_shift($pathParts);
        $testRoot = rtrim($testRoot, '/');
    }

    return $testRoot;
}

/**
 * Override wp_mail to not send mail.
 *
 * @param $to
 * @param $subject
 * @param $message
 * @param string $headers
 * @param array $attachments
 *
 * @return bool
 */
function wp_mail($to, $subject, $message, $headers = '', $attachments = array()) {
    return true;
}

$testRoot = getWpRootDir();

if (!is_dir($testRoot)) {
    exit(1);
}

require_once($testRoot . '/includes/functions.php');

function _manually_load_plugin() {
    require(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'wplog.php');
}

tests_add_filter('muplugins_loaded', '_manually_load_plugin');

require_once($testRoot . '/includes/bootstrap.php');
