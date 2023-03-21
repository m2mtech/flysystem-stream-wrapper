<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand;

use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;

final class StreamSeekCommand
{
    public static function run(FileData $current, int $offset, int $whence = SEEK_SET): bool
    {
        if (!is_resource($current->handle)) {
            return false;
        }

        return 0 === fseek($current->handle, $offset, $whence);
    }
}
