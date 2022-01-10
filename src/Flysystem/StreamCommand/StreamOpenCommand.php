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
use M2MTech\FlysystemStreamWrapper\Flysystem\Exception\FileNotFoundException;
use M2MTech\FlysystemStreamWrapper\Flysystem\Exception\InvalidStreamModeException;
use M2MTech\FlysystemStreamWrapper\Flysystem\Exception\UnableToReadException;
use M2MTech\FlysystemStreamWrapper\Flysystem\Exception\UnableToWriteException;
use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;

final class StreamOpenCommand
{
    use ExceptionHandler;

    public const OPEN_COMMAND = 'stream_open';

    public static function run(
        FileData $current,
        string $path,
        string $mode,
        int $options,
        ?string &$openedPath
    ): bool {
        $current->setPath($path);
        $filesystem = $current->filesystem;
        $file = $current->file;

        if (!preg_match('/^[rwacx](\+b?|b\+?)?$/', $mode)) {
            return self::triggerError(InvalidStreamModeException::atLocation(
                self::OPEN_COMMAND,
                $current->path,
                $mode
            ));
        }

        $current->writeOnly = !strpos($mode, '+');
        try {
            if ('r' === $mode[0] && $current->writeOnly) {
                $current->handle = $filesystem->readStream($file);
                $current->workOnLocalCopy = false;
                $current->writeOnly = false;
            } else {
                $current->handle = fopen('php://temp', 'w+b');
                $current->workOnLocalCopy = true;

                if ('w' !== $mode[0] && $filesystem->fileExists($file)) {
                    if ('x' === $mode[0]) {
                        throw UnableToWriteException::atLocation(self::OPEN_COMMAND, $current->path);
                    }

                    $result = false;
                    if (is_resource($current->handle)) {
                        $result = stream_copy_to_stream($filesystem->readStream($file), $current->handle);
                    }
                    if (!$result) {
                        throw UnableToWriteException::atLocation(self::OPEN_COMMAND, $current->path);
                    }
                }
            }

            $current->alwaysAppend = 'a' === $mode[0];
            if (is_resource($current->handle) && !$current->alwaysAppend) {
                rewind($current->handle);
            }
        } catch (FilesystemException $e) {
            if (($options & STREAM_REPORT_ERRORS) !== 0) {
                return self::triggerError(UnableToReadException::atLocation(
                    self::OPEN_COMMAND,
                    $current->path,
                    $e
                ));
            }

            return false;
        }

        if ($current->handle && $options & STREAM_USE_PATH) {
            $openedPath = $path;
        }

        if (is_resource($current->handle)) {
            return true;
        }

        if (($options & STREAM_REPORT_ERRORS) !== 0) {
            return self::triggerError(FileNotFoundException::atLocation(self::OPEN_COMMAND, $current->path));
        }

        return false;
    }
}
