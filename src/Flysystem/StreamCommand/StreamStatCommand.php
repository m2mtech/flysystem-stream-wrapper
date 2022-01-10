<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use M2MTech\FlysystemStreamWrapper\Flysystem\Exception\StatFailedException;
use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;

final class StreamStatCommand
{
    use ExceptionHandler;

    public const STAT_COMMAND = 'stream_stat';

    /** @return array<int|string,int|string>|false */
    public static function run(FileData $current)
    {
        try {
            return self::getStat($current);
        } catch (FilesystemException $e) {
            self::triggerError(StatFailedException::atLocation(self::STAT_COMMAND, $current->path, $e));

            return false;
        }
    }

    private const STATS_ZERO = [0, 'dev', 1, 'ino', 3, 'nlink', 4, 'uid', 5, 'gid', 6, 'rdev'];
    private const STATS_MODE = [2, 'mode'];
    private const STATS_SIZE = [7, 'size'];
    private const STATS_TIME = [8, 'atime', 9, 'mtime', 10, 'ctime'];
    private const STATS_MINUS_ONE = [11, 'blksize', 12, 'blocks'];

    /**
     * @return array<int|string,int|string>|false
     *
     * @throws FilesystemException
     */
    public static function getStat(FileData $current)
    {
        $stats = [];

        if ($current->workOnLocalCopy && is_resource($current->handle)) {
            $stats = fstat($current->handle);
            if (!$stats) {
                return false;
            }

            if ($current->filesystem->fileExists($current->file)) {
                [$mode, $size, $time] = self::getRemoteStats($current);

                unset($size);
            }
        } else {
            [$mode, $size, $time] = self::getRemoteStats($current);
        }

        foreach (self::STATS_ZERO as $key) {
            $stats[$key] = 0;
        }

        foreach (self::STATS_MINUS_ONE as $key) {
            $stats[$key] = -1;
        }

        if (isset($mode)) {
            foreach (self::STATS_MODE as $key) {
                $stats[$key] = $mode;
            }
        }

        if (isset($size)) {
            foreach (self::STATS_SIZE as $key) {
                $stats[$key] = $size;
            }
        }

        if (isset($time)) {
            foreach (self::STATS_TIME as $key) {
                $stats[$key] = $time;
            }
        }

        return $stats;
    }

    /**
     * @throws FilesystemException
     *
     * @return array<int,int>
     */
    public static function getRemoteStats(FileData $current): array
    {
        $converter = new PortableVisibilityConverter();
        $visibility = $current->filesystem->visibility($current->file);

        if ('directory' === $current->filesystem->mimeType($current->file)) {
            $mode = 040000 + $converter->forDirectory($visibility);
            $size = 0;
        } else {
            $mode = 0100000 + $converter->forFile($visibility);
            $size = $current->filesystem->fileSize($current->file);
        }

        $lastModified = $current->filesystem->lastModified($current->file);

        return [$mode, $size, $lastModified];
    }
}
