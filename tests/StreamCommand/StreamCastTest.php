<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\StreamCastCommand;
use PHPUnit\Framework\TestCase;

class StreamCastTest extends TestCase
{
    public function test(): void
    {
        $current = new FileData();

        $this->assertFalse(StreamCastCommand::run($current, STREAM_CAST_FOR_SELECT));

        $current->handle = fopen('php://temp', 'wb');

        $this->assertIsResource(StreamCastCommand::run($current, STREAM_CAST_FOR_SELECT));
    }
}
