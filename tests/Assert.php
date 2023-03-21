<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests;

use RuntimeException;

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
        clearstatcache();

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

    public function expectErrorWithMessage(string $message, int $code = E_USER_WARNING): void
    {
        set_error_handler(
            static function (int $errorCode, string $errorMessage) use ($code) {
                if ($code !== $errorCode) {
                    return true;
                }

                restore_error_handler();

                throw new RuntimeException($errorMessage, $errorCode);
            },
            E_ALL
        );

        $this->expectException(RuntimeException::class);
        if (0 === strpos($message, '/')) {
            $this->expectExceptionMessageMatches($message);
        } else {
            $this->expectExceptionMessage($message);
        }
    }
}
