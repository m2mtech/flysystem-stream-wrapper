<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand;

use Iterator;
use IteratorIterator;
use League\Flysystem\FilesystemException;
use M2MTech\FlysystemStreamWrapper\Flysystem\Exception\DirectoryNotFoundException;
use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;

final class DirOpendirCommand
{
    use ExceptionHandler;

    public const OPENDIR_COMMAND = 'dir_opendir';

    /** @noinspection PhpUnusedParameterInspection */
    public static function run(FileData $current, string $path, int $options): bool
    {
        $current->setPath($path);
        try {
            self::getDir($current);
        } catch (FilesystemException $e) {
            return self::triggerError(
                DirectoryNotFoundException::atLocation(self::OPENDIR_COMMAND, $path, $e)
            );
        }

        $valid = @is_dir($path);
        if (!$valid) {
            return self::triggerError(
                DirectoryNotFoundException::atLocation(self::OPENDIR_COMMAND, $path)
            );
        }

        return true;
    }

    /** @throws FilesystemException */
    public static function getDir(FileData $current): void
    {
        $listing = $current->filesystem->listContents($current->file)->getIterator();
        $current->dirListing = $listing instanceof Iterator ? $listing : new IteratorIterator($listing);
    }
}
