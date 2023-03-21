<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Flysystem\Exception;

use League\Flysystem\FilesystemException;
use RuntimeException;
use Throwable;

class StreamWrapperException extends RuntimeException implements FilesystemException
{
    protected const ERROR_MESSAGE = 'Error message not defined';

    public static function atLocation(
        string $command,
        string $location,
        Throwable $previous = null
    ): StreamWrapperException {
        return new self("$command($location): ".static::ERROR_MESSAGE, 0, $previous);
    }
}
