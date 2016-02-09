<?php

namespace Wplog\Tests\Logging;

use Wplog\Events\Options\OptionEvent;
use Wplog\Logging\LoggerCollection;
use Wplog\Logging\Wpdb\Logger as WpdbLogger;
use Wplog\Tests\WplogTestCase;

class LoggerCollectionTest extends WplogTestCase
{
    function testItCanAddLoggers()
    {
        $collection = new LoggerCollection();

        $this->assertEmpty($collection->getLoggers());

        $collection->addLogger(new WpdbLogger());

        $this->assertContainsOnlyInstancesOf(WpdbLogger::class, $collection->getLoggers());
    }

    function testItCanWriteLogCollection()
    {
        global $wpdb;

        $collection = new LoggerCollection();

        $collection->addLogger(new WpdbLogger());

        $event = new OptionEvent();
        $event->setBody('Hello World!');
        $event->setSeverity('notice');
        $event->setUserId('1');

        $collection->write($event);

        $row = $wpdb->get_row(
            sprintf(
                'SELECT * FROM %s.%s',
                DB_NAME,
                $wpdb->prefix.'wplog'
            )
        );

        $this->assertNotEmpty($row);
        $this->assertStringStartsWith('Hello World', $row->body);
    }
}
