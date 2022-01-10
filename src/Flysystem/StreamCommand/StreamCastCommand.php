<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand;

use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;

final class StreamCastCommand
{
    /**
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection PhpUnusedParameterInspection
     *
     * @return resource|false
     */
    public static function run(FileData $current, int $cast_as)
    {
        return $current->handle;
    }
}
