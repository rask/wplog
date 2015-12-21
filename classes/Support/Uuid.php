<?php
declare(strict_types=1);

namespace Wplog\Support;

use Ramsey\Uuid\Uuid as UuidLib;

/**
 * Class Uuid
 *
 * @since 0.1.0
 * @package Wplog\Support
 */
class Uuid
{
    /**
     * Get a time based UUID.
     *
     * @since 0.1.0
     * @return String
     */
    public static function getUuid() : string
    {
        $uuid = UuidLib::Uuid4();

        return $uuid->toString();
    }

    /**
     * Pack a string UUID to binary form.
     *
     * @param String $uuid The UUID to pack.
     *
     * @return mixed
     */
    public static function pack(string $uuid)
    {
        return pack('H*', str_replace('-', '', $uuid));
    }

    /**
     * Unpack a binary UUID to string format.
     *
     * @param $uuid
     *
     * @return String
     */
    public static function unpack($uuid) : string
    {
        $unpacked = unpack('H*', $uuid);
        $unpackString = array_shift($unpacked);

        $ptrn = '%([0-9a-f]{8})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{12})%';
        $repl = '$1-$2-$3-$4-$5';

        $stringUuid = preg_replace($ptrn, $repl, $unpackString);

        return $stringUuid;
    }
}
