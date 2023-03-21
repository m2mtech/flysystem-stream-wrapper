<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2022-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Flysystem\Helper;

class UserGuesser
{
    /** @var ?int */
    private static $uid = null;

    /** @var ?int */
    private static $gid = null;

    private static function useFallback(): void
    {
        self::$uid = (int) getmyuid();
        self::$gid = (int) getmygid();
    }

    private static function guess(): void
    {
        if (null !== self::$uid) {
            return;
        }

        $file = tempnam(sys_get_temp_dir(), 'UserGuesser');
        if (!$file) {
            self::useFallback();

            return;
        }

        file_put_contents($file, 'guessing');

        $stats = stat($file);
        if (!$stats) {
            self::useFallback();

            return;
        }

        self::$uid = $stats['uid'];
        self::$gid = $stats['gid'];

        unlink($file);
    }

    public static function getUID(): int
    {
        self::guess();

        return (int) self::$uid;
    }

    public static function getGID(): int
    {
        return (int) self::$gid;
    }
}
