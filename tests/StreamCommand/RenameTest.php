<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use League\Flysystem\UnableToMoveFile;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\RenameCommand;
use M2MTech\FlysystemStreamWrapper\Tests\Assert;
use PHPUnit\Framework\MockObject\MockObject;

class RenameTest extends AbstractStreamCommandTest
{
    use Assert;

    public function test(): void
    {
        $current = $this->getCurrent([
            'visibility' => 'public',
            'mimeType' => 'file',
        ]);
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->once())
            ->method('move')
            ->with('test', 'new');

        $this->assertTrue(RenameCommand::run($current, self::TEST_PATH, 'test://new'));
    }

    public function testSourceDoesNotExist(): void
    {
        $current = $this->getCurrent();

        $this->assertFalse(@RenameCommand::run($current, self::TEST_PATH, 'test://new'));

        $this->expectErrorWithMessage('No such file or directory');
        RenameCommand::run($current, self::TEST_PATH, 'test://new');
    }

    public function testFileToDirectory(): void
    {
        $current = $this->getCurrent([
            'visibility' => 'public',
        ]);
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->method('mimeType')
            ->willReturnCallback(function ($file) {
                if ('new' === $file) {
                    return 'directory';
                }

                return 'file';
            });

        $this->assertFalse(@RenameCommand::run($current, self::TEST_PATH, 'test://new'));

        $this->expectErrorWithMessage('Is a directory');
        RenameCommand::run($current, self::TEST_PATH, 'test://new');
    }

    public function testDirectoryToFile(): void
    {
        $current = $this->getCurrent([
            'visibility' => 'public',
        ]);
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->method('mimeType')
            ->willReturnCallback(function ($file) {
                if ('new' !== $file) {
                    return 'directory';
                }

                return 'file';
            });

        $this->assertFalse(@RenameCommand::run($current, self::TEST_PATH, 'test://new'));

        $this->expectErrorWithMessage('Not a directory');
        RenameCommand::run($current, self::TEST_PATH, 'test://new');
    }

    public function testFailedRemote(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->method('move')
            ->willThrowException(UnableToMoveFile::fromLocationTo('test', 'new'));

        $this->assertFalse(@RenameCommand::run($current, self::TEST_PATH, 'test://new'));

        @RenameCommand::run($current, self::TEST_PATH, 'test://new');
    }
}
