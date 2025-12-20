<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\Issues;

use League\Flysystem\FilesystemOperator;
use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamWrapper;
use M2MTech\FlysystemStreamWrapper\FlysystemStreamWrapper;
use M2MTech\FlysystemStreamWrapper\Tests\Assert;
use M2MTech\FlysystemStreamWrapper\Tests\StreamCommand\AbstractStreamCommandTestCase;

class AdapterClosesStreamTest extends AbstractStreamCommandTestCase
{
    use Assert;

    public function testStreamClose(): void
    {
        $current = $this->getCurrent();
        $current->handle = fopen('php://temp', 'wb');
        $wrapper = new StreamWrapper($current);

        if (!is_resource($current->handle)) {
            $this->fail();
        }

        // adapter closes stream itself
        fclose($current->handle);
        $this->assertIsClosedResource($current->handle);

        $wrapper->stream_close();
        $this->assertIsClosedResource($current->handle);
    }

    public function testStreamCloseWithLocalCopy(): void
    {
        $filesystem = $this->createMock(FilesystemOperator::class);
        FlysystemStreamWrapper::register(self::TEST_PROTOCOL, $filesystem);

        $current = new FileData();
        $current->setPath(self::TEST_PATH);

        $current->handle = fopen('php://temp', 'wb');
        $current->workOnLocalCopy = true;

        $wrapper = new StreamWrapper($current);

        if (!is_resource($current->handle)) {
            $this->fail();
        }

        $filesystem->expects($this->once())
            ->method('writeStream')
            ->willReturnCallback(static function (string $file, $handle) {
                // adapter closes the stream itself after writing it to file
                fclose($handle);
            });

        $wrapper->stream_close();
        $this->assertIsClosedResource($current->handle);
    }
}
