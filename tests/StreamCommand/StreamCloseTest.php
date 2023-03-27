<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use League\Flysystem\UnableToWriteFile;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamWrapper;
use M2MTech\FlysystemStreamWrapper\Tests\Assert;
use PHPUnit\Framework\MockObject\MockObject;

class StreamCloseTest extends AbstractStreamCommandTestCase
{
    use Assert;

    public function test(): void
    {
        $current = $this->getCurrent();
        $current->handle = fopen('php://temp', 'wb');
        $wrapper = new StreamWrapper($current);

        $this->assertIsNotClosedResource($current->handle);
        $wrapper->stream_close();
        $this->assertIsClosedResource($current->handle);
    }

    public function testWriteLocalCopy(): void
    {
        $current = $this->getCurrent();
        $current->handle = fopen('php://temp', 'wb');
        $current->workOnLocalCopy = true;
        $wrapper = new StreamWrapper($current);

        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->once())
            ->method('writeStream')
            ->with('test', $current->handle);

        $this->addToAssertionCount(1);

        $wrapper->stream_close();
    }

    public function testWriteLocalCopyRemoteFail(): void
    {
        $current = $this->getCurrent();
        $current->handle = fopen('php://temp', 'wb');
        $current->workOnLocalCopy = true;
        $wrapper = new StreamWrapper($current);

        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->method('writeStream')
            ->willThrowException(UnableToWriteFile::atLocation(self::TEST_PATH));

        $this->expectErrorWithMessage('Unable to sync file');
        $wrapper->stream_close();
    }
}
