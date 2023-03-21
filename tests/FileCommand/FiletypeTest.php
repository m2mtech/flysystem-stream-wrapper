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

class FiletypeTest extends AbstractFileCommandTest
{
    use Assert;

    public function test(): void
    {
        $file = $this->testDir->createFile(true);
        $this->assertSame('file', filetype($file->flysystem));

        $dir = $this->testDir->createDirectory(true);
        $this->assertSame('dir', filetype($dir->flysystem));
    }

    public function testFailed(): void
    {
        $file = $this->testDir->createFile();
        $this->assertFalse(@filetype($file->flysystem));

        $this->expectErrorWithMessage('Stat failed');
        /** @noinspection PhpUnusedLocalVariableInspection */
        $type = filetype($file->flysystem);
    }
}
