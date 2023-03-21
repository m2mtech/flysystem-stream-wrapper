<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class IsReadableTest extends AbstractFileCommandTest
{
    public function test(): void
    {
        $file = $this->testDir->createFile();
        $this->assertFalse(is_readable($file->flysystem));

        $file = $this->testDir->createFile(true);
        $this->assertTrue(is_readable($file->flysystem));

        $this->assertTrue(is_readable($this->testDir->flysystem));
    }
}
