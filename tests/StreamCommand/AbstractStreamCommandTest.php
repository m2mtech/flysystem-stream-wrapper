<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use League\Flysystem\FilesystemOperator;
use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;
use M2MTech\FlysystemStreamWrapper\FlysystemStreamWrapper;
use PHPUnit\Framework\TestCase;

abstract class AbstractStreamCommandTest extends TestCase
{
    public const TEST_PROTOCOL = 'test';
    public const TEST_PATH = 'test://test';

    /** @param array<string, string|bool> $methods */
    public function getFilesystem(array $methods = []): FilesystemOperator
    {
        $filesystem = $this->createMock(FilesystemOperator::class);
        foreach ($methods as $method => $return) {
            if ('directoryExists' === $method) {
                continue;
            }

            $filesystem->method($method)->willReturn($return);
        }

        FlysystemStreamWrapper::register(self::TEST_PROTOCOL, $filesystem);

        return $filesystem;
    }

    /** @param array<string, string|bool> $methods */
    public function getCurrent(array $methods = []): FileData
    {
        $this->getFilesystem($methods);

        $current = new FileData();
        $current->setPath(self::TEST_PATH);

        return $current;
    }

    public function tearDown(): void
    {
        FlysystemStreamWrapper::unregisterAll();

        parent::tearDown();
    }
}
