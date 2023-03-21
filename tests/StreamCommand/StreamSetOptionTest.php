<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\StreamSetOptionCommand;
use M2MTech\FlysystemStreamWrapper\Tests\FileCommand\AbstractFileCommandTest;

class StreamSetOptionTest extends AbstractFileCommandTest
{
    public function test(): void
    {
        $current = new FileData();

        $this->assertFalse(StreamSetOptionCommand::run($current, 42, 42, 42));
    }

    public function testOptionBlocking(): void
    {
        $current = new FileData();

        $this->assertFalse(StreamSetOptionCommand::run($current, STREAM_OPTION_BLOCKING, 0, 0));

        $file = $this->testDir->createFile(true);
        $current->handle = fopen($file->local, 'rb');
        if (!is_resource($current->handle)) {
            $this->fail();
        }

        $this->assertTrue(StreamSetOptionCommand::run($current, STREAM_OPTION_BLOCKING, 0, 0));
        $this->assertFalse(stream_get_meta_data($current->handle)['blocked']);

        $this->assertTrue(StreamSetOptionCommand::run($current, STREAM_OPTION_BLOCKING, 1, 0));
        $this->assertTrue(stream_get_meta_data($current->handle)['blocked']);
    }

    public function testOptionReadBuffer(): void
    {
        $current = new FileData();

        $this->assertFalse(StreamSetOptionCommand::run($current, STREAM_OPTION_READ_BUFFER, STREAM_BUFFER_NONE, 0));

        $file = $this->testDir->createFile(true);
        $current->handle = fopen($file->local, 'rb');
        $this->assertTrue(StreamSetOptionCommand::run($current, STREAM_OPTION_READ_BUFFER, STREAM_BUFFER_NONE, 0));
        $this->assertTrue(StreamSetOptionCommand::run($current, STREAM_OPTION_READ_BUFFER, STREAM_BUFFER_LINE, 42));
        $this->assertTrue(StreamSetOptionCommand::run($current, STREAM_OPTION_READ_BUFFER, STREAM_BUFFER_FULL, 1024));
    }

    public function testOptionWriteBuffer(): void
    {
        $current = new FileData();

        $this->assertFalse(StreamSetOptionCommand::run($current, STREAM_OPTION_WRITE_BUFFER, STREAM_BUFFER_NONE, 0));

        $file = $this->testDir->createFile(true);
        $current->handle = fopen($file->local, 'rb');
        $this->assertTrue(StreamSetOptionCommand::run($current, STREAM_OPTION_WRITE_BUFFER, STREAM_BUFFER_NONE, 0));
        $this->assertSame(0, $current->writeBufferSize);

        $this->assertTrue(StreamSetOptionCommand::run($current, STREAM_OPTION_WRITE_BUFFER, STREAM_BUFFER_LINE, 42));
        $this->assertSame(42, $current->writeBufferSize);

        $this->assertTrue(StreamSetOptionCommand::run($current, STREAM_OPTION_WRITE_BUFFER, STREAM_BUFFER_FULL, 1024));
        $this->assertSame(1024, $current->writeBufferSize);
    }

    public function testOptionReadTimeout(): void
    {
        $current = new FileData();

        $this->assertFalse(StreamSetOptionCommand::run($current, STREAM_OPTION_READ_TIMEOUT, 0, 0));

        $file = $this->testDir->createFile(true);
        $current->handle = fopen($file->local, 'rb');
        if (!is_resource($current->handle)) {
            $this->fail();
        }

        $this->assertSame(
            stream_set_timeout($current->handle, 30),
            StreamSetOptionCommand::run($current, STREAM_OPTION_READ_TIMEOUT, 30, 0)
        );
    }
}
