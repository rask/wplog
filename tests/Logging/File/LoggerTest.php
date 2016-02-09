<?php

namespace Wplog\Tests\Logging\File;

use Wplog\Events\Options\OptionEvent;
use Wplog\Tests\WplogTestCase;

/**
 * Class LoggerTest
 *
 * @package Wplog\Tests\Logging\File
 */
class LoggerTest extends WplogTestCase
{
    public function testItLogsToFile()
    {
        $logger = new \Wplog\Logging\File\Logger();

        $testLogFile = ABSPATH . '/wplog-testing.log';

        $logger->setLogFile($testLogFile);

        $event = new OptionEvent();

        $event->setBody('Hello World {user}!');
        $event->setSeverity('notice');
        $event->setContext(['user' => 'John Doe']);

        $logger->debug('Hello World!', [], $event);

        $logContents = file_get_contents($testLogFile);

        $this->assertRegExp('%Hello World John Doe%imu', $logContents);

        unlink($testLogFile);
    }
}
