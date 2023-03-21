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
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\StreamTruncateCommand;
use PHPUnit\Framework\TestCase;

class StreamTruncateTest extends TestCase
{
    public function test(): void
    {
        $current = new FileData();

        $this->assertFalse(StreamTruncateCommand::run($current, 42));

        $current->handle = fopen('php://temp', 'wb+');
        if (!is_resource($current->handle)) {
            $this->fail();
        }

        fwrite($current->handle, 'test');
        $this->assertTrue(StreamTruncateCommand::run($current, 2));

        fseek($current->handle, 0, SEEK_END);
        $this->assertSame(2, ftell($current->handle));

        $this->assertTrue(StreamTruncateCommand::run($current, 42));

        rewind($current->handle);
        $content = (string) fread($current->handle, 1024);
        $this->assertSame(42, strlen($content));
        $this->assertSame('te', trim($content));
    }
}
