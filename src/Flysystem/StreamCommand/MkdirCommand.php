<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand;

use League\Flysystem\Config;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use M2MTech\FlysystemStreamWrapper\Flysystem\Exception\DirectoryExistsException;
use M2MTech\FlysystemStreamWrapper\Flysystem\Exception\UnableToCreateDirectoryException;
use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;

final class MkdirCommand
{
    use ExceptionHandler;

    public const MKDIR_COMMAND = 'mkdir';

    /** @noinspection PhpUnusedParameterInspection */
    public static function run(FileData $current, string $path, int $mode, int $options): bool
    {
        if (file_exists($path)) {
            return self::triggerError(DirectoryExistsException::atLocation(self::MKDIR_COMMAND, $path));
        }

        $current->setPath($path);

        try {
            $visibility = new PortableVisibilityConverter();
            $config = [
                Config::OPTION_VISIBILITY => $visibility->inverseForDirectory($mode),
            ];
            $current->filesystem->createDirectory($current->file, $config);

            return true;
        } catch (FilesystemException $e) {
            return self::triggerError(
                UnableToCreateDirectoryException::atLocation(self::MKDIR_COMMAND, $path, $e)
            );
        }
    }
}
