<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\Visibility;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\StreamStatCommand;
use M2MTech\FlysystemStreamWrapper\Tests\Assert;
use PHPUnit\Framework\MockObject\MockObject;

class StreamStatTest extends AbstractStreamCommandTest
{
    use Assert;

    public function test(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->once())
            ->method('visibility')
            ->with('test')
            ->willReturn(Visibility::PUBLIC);

        $stats = StreamStatCommand::run($current);
        $this->assertIsArray($stats);
    }

    public function testVisibilityForFile(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->exactly(2))
            ->method('visibility')
            ->with('test')
            ->willReturnOnConsecutiveCalls(
                Visibility::PUBLIC,
                Visibility::PRIVATE
            );

        try {
            $stats = StreamStatCommand::getRemoteStats($current);
            $this->assertSame(0100644, $stats[0]);

            $stats = StreamStatCommand::getRemoteStats($current);
            $this->assertSame(0100600, $stats[0]);
        } catch (FilesystemException $e) {
            $this->fail();
        }
    }

    public function testVisibilityForDirectory(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->exactly(2))
            ->method('mimeType')
            ->with('test')
            ->willReturn('directory');
        $filesystem->expects($this->exactly(2))
            ->method('visibility')
            ->with('test')
            ->willReturnOnConsecutiveCalls(
                Visibility::PUBLIC,
                Visibility::PRIVATE
            );

        try {
            $stats = StreamStatCommand::getRemoteStats($current);
            $this->assertSame(040755, $stats[0]);

            $stats = StreamStatCommand::getRemoteStats($current);
            $this->assertSame(040700, $stats[0]);
        } catch (FilesystemException $e) {
            $this->fail();
        }
    }

    public function testSizeForFile(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->once())
            ->method('visibility')
            ->with('test')
            ->willReturn(Visibility::PUBLIC);
        $filesystem->expects($this->once())
            ->method('fileSize')
            ->with('test')
            ->willReturn(42);

        try {
            $stats = StreamStatCommand::getRemoteStats($current);
            $this->assertSame(42, $stats[1]);
        } catch (FilesystemException $e) {
            $this->fail();
        }
    }

    public function testSizeForDirectory(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->once())
            ->method('mimeType')
            ->with('test')
            ->willReturn('directory');
        $filesystem->expects($this->once())
            ->method('visibility')
            ->with('test')
            ->willReturn(Visibility::PUBLIC);
        $filesystem->expects($this->never())
            ->method('fileSize');

        try {
            $stats = StreamStatCommand::getRemoteStats($current);
            $this->assertSame(0, $stats[1]);
        } catch (FilesystemException $e) {
            $this->fail();
        }
    }

    public function testLastModified(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->once())
            ->method('visibility')
            ->with('test')
            ->willReturn(Visibility::PUBLIC);
        $now = time();
        $filesystem->expects($this->once())
            ->method('lastModified')
            ->with('test')
            ->willReturn($now);

        try {
            $stats = StreamStatCommand::getRemoteStats($current);
            $this->assertSame($now, $stats[2]);
        } catch (FilesystemException $e) {
            $this->fail();
        }
    }

    public function testRemoteException(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->method('visibility')
            ->willThrowException(UnableToRetrieveMetadata::visibility(self::TEST_PATH));

        $this->assertFalse(@StreamStatCommand::run($current));

        $this->expectErrorWithMessage('Stat failed');
        StreamStatCommand::run($current);
    }

    public function testLocalStats(): void
    {
        $current = $this->getCurrent();
        $current->workOnLocalCopy = true;
        $current->handle = fopen('php://temp', 'wb');
        if (!is_resource($current->handle)) {
            $this->fail();
        }
        fwrite($current->handle, 'test');

        $stats = StreamStatCommand::run($current);
        if (!is_array($stats)) {
            $this->fail();
        }

        $this->assertSame(4, $stats['size']);
        $this->assertSame(0100000, 0100000 & (int) $stats['mode']);
    }

    public function testLocalStatsOverwrittenByRemote(): void
    {
        $current = $this->getCurrent();
        $current->workOnLocalCopy = true;
        $current->handle = fopen('php://temp', 'wb');
        if (!is_resource($current->handle)) {
            $this->fail();
        }
        fwrite($current->handle, 'test');

        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->once())
            ->method('fileExists')
            ->with('test')
            ->willReturn(true);
        $filesystem->expects($this->once())
            ->method('mimeType')
            ->with('test')
            ->willReturn('directory');
        $filesystem->expects($this->once())
            ->method('visibility')
            ->with('test')
            ->willReturn(Visibility::PUBLIC);

        $stats = StreamStatCommand::run($current);
        if (!is_array($stats)) {
            $this->fail();
        }

        // from local
        $this->assertSame(4, $stats['size']);

        // from remote
        $this->assertSame(040000, 040000 & (int) $stats['mode']);
        $this->assertSame(0, $stats['atime']);
    }
}
