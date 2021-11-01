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

class StreamReadCommand
{
    public static function run(FileData $current, int $count): string
    {
        if ($current->writeOnly || !is_resource($current->handle) || $count < 0) {
            return '';
        }

        return (string) fread($current->handle, $count);
    }
}
