<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand;

use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;

final class StreamWriteCommand
{
    public static function run(FileData $current, string $data): int
    {
        if (!is_resource($current->handle)) {
            return 0;
        }

        if ($current->alwaysAppend) {
            fseek($current->handle, 0, SEEK_END);
        }

        $size = (int) fwrite($current->handle, $data);
        $current->bytesWritten += $size;

        if ($current->alwaysAppend) {
            fseek($current->handle, 0, SEEK_SET);
        }

        return $size;
    }
}
