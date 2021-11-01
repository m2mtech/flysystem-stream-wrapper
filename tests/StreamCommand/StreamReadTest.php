<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\StreamReadCommand;
use PHPUnit\Framework\TestCase;

class StreamReadTest extends TestCase
{
    public function test(): void
    {
        $current = new FileData();
        $this->assertEmpty(StreamReadCommand::run($current, 42));

        $current->writeOnly = true;
        $current->handle = fopen(__FILE__, 'rb');
        $this->assertEmpty(StreamReadCommand::run($current, 42));

        $current->writeOnly = false;
        $content = StreamReadCommand::run($current, 42);
        $this->assertSame(42, strlen($content));
    }
}
