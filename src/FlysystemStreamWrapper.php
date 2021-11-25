<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper;

use League\Flysystem\FilesystemOperator;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamWrapper;

final class FlysystemStreamWrapper
{
    public const LOCK_STORE = 'lock_store';
    public const LOCK_TTL = 'lock_ttl';

    public const DEFAULT_CONFIGURATION = [
        self::LOCK_STORE => 'flock:///tmp',
        self::LOCK_TTL => 300,
    ];

    /** @var array <string, FilesystemOperator> */
    public static $filesystems = [];

    /** @var array <string, array<string, int|string>> */
    public static $config = [];

    /** @param array<string, int|string> $configuration */
    public static function register(
        string $protocol,
        FilesystemOperator $filesystem,
        array $configuration = [],
        int $flags = 0
    ): bool {
        if (self::streamWrapperExists($protocol)) {
            return false;
        }

        self::$config[$protocol] = array_merge(self::DEFAULT_CONFIGURATION, $configuration);
        self::$filesystems[$protocol] = $filesystem;

        return stream_wrapper_register($protocol, StreamWrapper::class, $flags);
    }

    public static function unregister(string $protocol): bool
    {
        if (!self::streamWrapperExists($protocol)) {
            return false;
        }

        unset(self::$config[$protocol], self::$filesystems[$protocol]);

        return stream_wrapper_unregister($protocol);
    }

    public static function unregisterAll(): void
    {
        foreach (self::getRegisteredProtocols() as $protocol) {
            self::unregister($protocol);
        }
    }

    /** @return array<int, string> */
    public static function getRegisteredProtocols(): array
    {
        return array_keys(self::$filesystems);
    }

    public static function streamWrapperExists(string $protocol): bool
    {
        return in_array($protocol, stream_get_wrappers(), true);
    }
}
