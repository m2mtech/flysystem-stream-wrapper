<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests;

/**
 * @method assertDirectoryDoesNotExist(string $local)
 * @method assertFileDoesNotExist(string $file)
 * @method assertIsClosedResource(resource|false $file)
 * @method assertIsNotClosedResource(resource|false $file)
 */
trait Assert
{
    public function assertPermission(string $file, int $permission): void
    {
        clearstatcache(false);

        $this->assertSame(decoct($permission & 0777), decoct(fileperms($file) & 0777));
    }

    /**
     * @param array<string> $args
     * @noinspection PhpDeprecationInspection
     * @noinspection RedundantSuppression
     * @noinspection UnknownInspectionInspection
     */
    public function __call(string $method, array $args): void
    {
        if ('assertDirectoryDoesNotExist' === $method) {
            $this->assertDirectoryNotExists(...$args);
        }
        if ('assertFileDoesNotExist' === $method) {
            $this->assertFileNotExists(...$args);
        }
        if ('assertIsNotClosedResource' === $method) {
            $this->assertIsResource(...$args);
        }
        if ('assertIsClosedResource' === $method) {
            $this->assertIsResource(...$args);
        }
    }
}
