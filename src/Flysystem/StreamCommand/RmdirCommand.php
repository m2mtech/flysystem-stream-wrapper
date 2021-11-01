<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand;

use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use League\Flysystem\WhitespacePathNormalizer;
use M2MTech\FlysystemStreamWrapper\Flysystem\Exception\CouldNotRemoveDirectoryException;
use M2MTech\FlysystemStreamWrapper\Flysystem\Exception\DirectoryNotEmptyException;
use M2MTech\FlysystemStreamWrapper\Flysystem\Exception\RootDirectoryException;
use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;

class RmdirCommand
{
    use ExceptionHandler;

    public const RMDIR_COMMAND = 'rmdir';

    public static function run(FileData $current, string $path, int $options): bool
    {
        $current->setPath($path);

        $n = new WhitespacePathNormalizer();
        $n->normalizePath($current->file);
        if (!$n->normalizePath($current->file)) {
            return self::triggerError(
                RootDirectoryException::atLocation(self::RMDIR_COMMAND, $current->path)
            );
        }

        if ($options & STREAM_MKDIR_RECURSIVE) {
            return self::rmdir($current);
        }

        try {
            $listing = $current->filesystem->listContents($current->file);
        } catch (FilesystemException $e) {
            return self::triggerError(
                DirectoryNotEmptyException::atLocation(self::RMDIR_COMMAND, $current->path)
            );
        }

        foreach ($listing as $ignored) {
            if (!$ignored instanceof StorageAttributes) {
                continue;
            }

            return self::triggerError(
                DirectoryNotEmptyException::atLocation(self::RMDIR_COMMAND, $current->path)
            );
        }

        return self::rmdir($current);
    }

    private static function rmdir(FileData $current): bool
    {
        try {
            $current->filesystem->deleteDirectory($current->file);

            return true;
        } catch (FilesystemException $e) {
            return self::triggerError(
                CouldNotRemoveDirectoryException::atLocation(self::RMDIR_COMMAND, $current->path, $e)
            );
        }
    }
}
