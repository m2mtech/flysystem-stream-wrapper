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
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\StreamSeekCommand;
use PHPUnit\Framework\TestCase;

class StreamSeekTest extends TestCase
{
    public function test(): void
    {
        $current = new FileData();

        $this->assertFalse(StreamSeekCommand::run($current, 0));

        $end = filesize(__FILE__);
        $current->handle = fopen(__FILE__, 'rb');
        if (!is_resource($current->handle)) {
            $this->fail();
        }

        $this->assertTrue(StreamSeekCommand::run($current, 23));
        $this->assertSame(23, ftell($current->handle));

        $this->assertTrue(StreamSeekCommand::run($current, 23, SEEK_CUR));
        $this->assertSame(2 * 23, ftell($current->handle));

        $this->assertTrue(StreamSeekCommand::run($current, -23, SEEK_END));
        $this->assertSame($end - 23, ftell($current->handle));
    }
}
