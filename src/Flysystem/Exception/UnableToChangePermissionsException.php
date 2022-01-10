<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Flysystem\Exception;

use League\Flysystem\FilesystemException;
use RuntimeException;
use Throwable;

final class UnableToChangePermissionsException extends RuntimeException implements FilesystemException
{
    public static function atLocation(
        string $command,
        string $location,
        string $permission,
        Throwable $previous = null
    ): UnableToChangePermissionsException {
        return new self("$command($location,$permission): Unable to change permissions", 0, $previous);
    }
}
