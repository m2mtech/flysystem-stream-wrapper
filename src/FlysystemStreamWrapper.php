<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper;

use League\Flysystem\FilesystemOperator;
use League\Flysystem\Visibility;
use M2MTech\FlysystemStreamWrapper\Flysystem\Helper\UserGuesser;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamWrapper;

final class FlysystemStreamWrapper
{
    public const LOCK_STORE = 'lock_store';
    public const LOCK_TTL = 'lock_ttl';

    /** @deprecated 2.0.0 use FlysystemStreamWrapper::IGNORE_VISIBILITY_ERRORS instead */
    public const IGNORE_VISIBILITY_ERROS = 'ignore_visibility_errors';

    public const IGNORE_VISIBILITY_ERRORS = 'ignore_visibility_errors';

    public const UID = 'uid';
    public const GID = 'gid';

    public const VISIBILITY_FILE_PUBLIC = 'visibility_file_public';
    public const VISIBILITY_FILE_PRIVATE = 'visibility_file_private';
    public const VISIBILITY_DIRECTORY_PUBLIC = 'visibility_directory_public';
    public const VISIBILITY_DIRECTORY_PRIVATE = 'visibility_directory_private';
    public const VISIBILITY_DEFAULT_FOR_DIRECTORIES = 'visibility_default_for_directories';

    public const DEFAULT_CONFIGURATION = [
        self::LOCK_STORE => 'flock:///tmp',
        self::LOCK_TTL => 300,

        self::IGNORE_VISIBILITY_ERRORS => false,

        self::UID => null,
        self::GID => null,

        self::VISIBILITY_FILE_PUBLIC => 0644,
        self::VISIBILITY_FILE_PRIVATE => 0600,
        self::VISIBILITY_DIRECTORY_PUBLIC => 0755,
        self::VISIBILITY_DIRECTORY_PRIVATE => 0700,
        self::VISIBILITY_DEFAULT_FOR_DIRECTORIES => Visibility::PRIVATE,
    ];

    /** @var array <string, FilesystemOperator> */
    public static $filesystems = [];

    /** @var array <string, array<string, int|string|bool|null>> */
    public static $config = [];

    /** @param array<string, bool|int|string> $configuration */
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

        if (null === self::$config[$protocol][self::UID]) {
            self::$config[$protocol][self::UID] = UserGuesser::getUID();
        }

        if (null === self::$config[$protocol][self::GID]) {
            self::$config[$protocol][self::GID] = UserGuesser::getGID();
        }

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
