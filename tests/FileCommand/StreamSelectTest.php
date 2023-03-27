<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class StreamSelectTest extends AbstractFileCommandTestCase
{
    public function test(): void
    {
        $fileRead1 = $this->testDir->createFile(true);
        $fileRead2 = $this->testDir->createFile();
        $handleRead1 = fopen($fileRead1->flysystem, 'rb');
        $handleRead2 = fopen($fileRead2->flysystem, 'rb+');
        if (!is_resource($handleRead1) || !is_resource($handleRead2)) {
            $this->fail();
        }
        $read = [$handleRead1, $handleRead2];

        $fileWriteExisting = $this->testDir->createFile(true);
        $fileWriteNotExisting = $this->testDir->createFile();
        $handleWriteExisting = fopen($fileWriteExisting->flysystem, 'ab+');
        $handleWriteNotExisting = fopen($fileWriteNotExisting->flysystem, 'wb+');
        if (!is_resource($handleWriteExisting) || !is_resource($handleWriteNotExisting)) {
            $this->fail();
        }
        $write = [$handleWriteExisting, $handleWriteNotExisting];

        $fileExcept = $this->testDir->createFile(true);
        $handleExcept = fopen($fileExcept->flysystem, 'rb');
        if (!is_resource($handleExcept)) {
            $this->fail();
        }
        $except = [$handleExcept];

        $this->assertEqualsIgnoringCase(4, stream_select($read, $write, $except, 0));
        $this->assertEmpty($except);
    }
}
