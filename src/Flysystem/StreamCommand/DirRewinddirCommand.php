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
use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;

final class DirRewinddirCommand
{
    use ExceptionHandler;

    public static function run(FileData $current): bool
    {
        try {
            DirOpendirCommand::getDir($current);
        } catch (FilesystemException $e) {
            return self::triggerError($e);
        }

        return true;
    }
}
