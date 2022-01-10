<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

use M2MTech\FlysystemStreamWrapper\Tests\Assert;

class IsDirTest extends AbstractFileCommandTest
{
    use Assert;

    /** @noinspection PhpUnitTestsInspection */
    public function test(): void
    {
        $dir = $this->testDir->createDirectory();
        $this->assertFalse(is_dir($dir->flysystem));

        mkdir($dir->local);
        $this->assertTrue(is_dir($dir->flysystem));
    }

    /** @noinspection PhpUnitTestsInspection */
    public function testFile(): void
    {
        $file = $this->testDir->createFile(true);
        $this->assertFalse(is_dir($file->flysystem));
    }
}
