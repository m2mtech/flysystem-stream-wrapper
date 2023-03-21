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

final class StreamSetOptionCommand
{
    public static function run(FileData $current, int $option, int $arg1, ?int $arg2): bool
    {
        if (!is_resource($current->handle)) {
            return false;
        }

        switch ($option) {
            case STREAM_OPTION_BLOCKING:
                return stream_set_blocking($current->handle, 1 === $arg1);

            case STREAM_OPTION_READ_BUFFER:
                return 0 === stream_set_read_buffer(
                    $current->handle,
                    STREAM_BUFFER_NONE === $arg1 ? 0 : (int) $arg2
                );

            case STREAM_OPTION_WRITE_BUFFER:
                $current->writeBufferSize = STREAM_BUFFER_NONE === $arg1 ? 0 : (int) $arg2;

                return true;

            case STREAM_OPTION_READ_TIMEOUT:
                return stream_set_timeout($current->handle, $arg1, (int) $arg2);
        }

        return false;
    }
}
