<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

use M2MTech\FlysystemStreamWrapper\Tests\Assert;

class FileatimeTest extends AbstractFileCommandTestCase
{
    use Assert;

    public function test(): void
    {
        $file = $this->testDir->createFile(true);
        $this->assertSame(filemtime($file->local), fileatime($file->flysystem));
    }

    public function testFailed(): void
    {
        $file = $this->testDir->createFile();
        $this->assertFalse(@fileatime($file->flysystem));

        $this->expectErrorWithMessage('Stat failed');
        /** @noinspection PhpUnusedLocalVariableInspection */
        $time = fileatime($file->flysystem);
    }
}
