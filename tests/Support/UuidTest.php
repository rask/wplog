<?php

namespace Wplog\Tests;

use Wplog\Support\Uuid;

/**
 * Class UuidTest
 *
 * @package Wplog\Tests
 */
class UuidTest extends WplogTestCase
{
    function testItGeneratesUuid()
    {
        $uuid = Uuid::getUuid();

        $this->assertRegExp('%^[0-9A-Za-z-]+$%', $uuid);
    }

    function testItPackUuidsToBinaryForm()
    {
        $uuid = Uuid::getUuid();

        $this->assertRegExp('%^[0-9A-Za-z-]+$%', $uuid);

        $packed = Uuid::pack($uuid);
        $packedLength = strlen($packed);

        $this->assertEquals(16, $packedLength);
    }

    function testItUnpacksUuidsFromBinaryForm()
    {
        $uuid = Uuid::getUuid();
        $packed = Uuid::pack($uuid);
        $unpacked = Uuid::unpack($packed);

        $this->assertRegExp('%^[0-9A-Za-z-]+$%', $unpacked);
        $this->assertEquals($unpacked, $uuid);
    }
}
