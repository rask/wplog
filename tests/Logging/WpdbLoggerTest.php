<?php

namespace Wplog\Tests\Logging;

use Wplog\Events\Options\OptionEvent;
use Wplog\Logging\WpdbLogger;
use Wplog\Tests\WplogTestCase;
use Psr\Log\LogLevel;

class WpdbLoggerTest extends WplogTestCase
{
    function testItWritesLogsToDatabase()
    {
        global $wpdb;

        $logger = new WpdbLogger();
        $event = new OptionEvent();

        $event->setBody('Hello World {user}!');
        $event->setSeverity('notice');
        $event->setContext(['user' => 'John Doe']);

        $logger->log('notice', 'Hello World {user}!', ['user' => 'John Doe'], $event);

        $result = $wpdb->get_row(
            sprintf(
                'SELECT * FROM %s.%s WHERE body LIKE "%%World%%"',
                DB_NAME,
                $wpdb->prefix.'wplog'
            )
        );

        $this->assertNotEmpty($result);
        $this->assertStringStartsWith('Hello World', $result->body);
        $this->assertStringEndsWith('Doe!', $result->body);
    }
}
