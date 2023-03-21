<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Flysystem;

use Iterator;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;
use M2MTech\FlysystemStreamWrapper\FlysystemStreamWrapper;
use Symfony\Component\Lock\Key;

final class FileData
{
    /** @var string */
    public $path;

    /** @var string */
    public $protocol;

    /** @var string */
    public $file;

    /** @var FilesystemOperator */
    public $filesystem;

    /** @var array<string, int|string|bool|null> */
    public $config = [];

    /** @var resource|false */
    public $handle = false;

    /** @var bool */
    public $writeOnly = false;

    /** @var bool */
    public $alwaysAppend = false;

    /** @var bool */
    public $workOnLocalCopy = false;

    /** @var int */
    public $writeBufferSize = 0;

    /** @var int */
    public $bytesWritten = 0;

    /** @var Key */
    public $lockKey;

    /** @var Iterator<mixed,StorageAttributes> */
    public $dirListing;

    public function setPath(string $path): void
    {
        $this->path = $path;
        $this->protocol = substr($path, 0, (int) strpos($path, '://'));
        $this->file = self::getFile($path);
        $this->filesystem = FlysystemStreamWrapper::$filesystems[$this->protocol];
        $this->config = FlysystemStreamWrapper::$config[$this->protocol];
    }

    public static function getFile(string $path): string
    {
        return (string) substr($path, strpos($path, '://') + 3);
    }

    public function ignoreVisibilityErrors(): bool
    {
        return (bool) $this->config[FlysystemStreamWrapper::IGNORE_VISIBILITY_ERRORS];
    }
}
