<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand;

use League\Flysystem\FilesystemException;
use Throwable;

trait ExceptionHandler
{
    public static function triggerError(FilesystemException $e): bool
    {
        trigger_error(self::collectErrorMessage($e), E_USER_WARNING);

        return false;
    }

    protected static function collectErrorMessage(Throwable $e): string
    {
        $message = $e->getMessage();
        $previous = $e->getPrevious();
        if (!$previous instanceof Throwable) {
            return $message;
        }

        return $message.' : '.self::collectErrorMessage($previous);
    }
}
