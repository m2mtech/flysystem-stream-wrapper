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
use M2MTech\FlysystemStreamWrapper\Flysystem\Exception\UnableToChangePermissionsException;
use M2MTech\FlysystemStreamWrapper\Flysystem\Exception\UnableToWriteException;
use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;

final class StreamMetadataCommand
{
    use ExceptionHandler;

    public const METADATA_COMMAND = 'stream_metadata';

    /** @param mixed $value */
    public static function run(FileData $current, string $path, int $option, $value): bool
    {
        $current->setPath($path);
        $filesystem = $current->filesystem;
        $file = $current->file;

        switch ($option) {
            case STREAM_META_ACCESS:
                if (!is_int($value)) {
                    /* @phpstan-ignore-next-line */
                    $value = (int) $value;
                }

                $converter = new PortableVisibilityConverter();
                $visibility = is_dir($path) ? $converter->inverseForDirectory($value) : $converter->inverseForFile($value);

                try {
                    $filesystem->setVisibility($file, $visibility);
                } catch (FilesystemException $e) {
                    return self::triggerError(UnableToChangePermissionsException::atLocation(
                        self::METADATA_COMMAND,
                        $current->path,
                        decoct($value),
                        $e
                    ));
                }

                return true;

            case STREAM_META_TOUCH:
                try {
                    if (!$filesystem->fileExists($file)) {
                        $filesystem->write($file, '');
                    }
                } catch (FilesystemException $e) {
                    return self::triggerError(UnableToWriteException::atLocation(
                        self::METADATA_COMMAND,
                        $current->path,
                        $e
                    ));
                }

                return true;

            default:
                return false;
        }
    }
}
