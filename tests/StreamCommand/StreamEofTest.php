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
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\StreamEofCommand;
use PHPUnit\Framework\TestCase;

class StreamEofTest extends TestCase
{
    public function test(): void
    {
        $current = new FileData();
        $this->assertFalse(StreamEofCommand::run($current));

        $current->handle = fopen('php://temp', 'w+b');
        if (!is_resource($current->handle)) {
            $this->fail();
        }

        $this->assertFalse(StreamEofCommand::run($current));

        fread($current->handle, 1);
        $this->assertTrue(StreamEofCommand::run($current));
    }
}
