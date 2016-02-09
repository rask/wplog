<?php

namespace Wplog\Tests;

use Wplog\Logging\InternalLogger;

/**
 * Class InternalLoggerTest
 *
 * @package Wplog\Tests
 */
class InternalLoggerTest extends WplogTestCase
{
    public function testItLogsToLogFile()
    {
        $logFile = wp_upload_dir()['path'] . '/wplog.internal.test.log';

        if (!is_file($logFile)) {
            file_put_contents($logFile, '');
        }

        $logger = new InternalLogger();
        $logger->setLogFile($logFile);

        $logger->debug('This is a test log line.');

        $contents = file_get_contents($logFile);

        $this->assertRegExp('%test log line\.%imu', $contents);

        unlink($logFile);
    }

    public function testItIsUsableThroughPluginInstance()
    {
        /** @var \Wplog\Plugin $plugin */
        $plugin = \Wplog\wplog();

        /** @var \Wplog\Logging\InternalLogger $logger */
        $logger = $plugin->internalLogger();

        $this->assertInstanceOf(InternalLogger::class, $logger);
    }
}
