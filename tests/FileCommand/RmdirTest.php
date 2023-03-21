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

class RmdirTest extends AbstractFileCommandTest
{
    use Assert;

    public function test(): void
    {
        $dir = $this->testDir->createDirectory(true);

        $this->assertDirectoryExists($dir->local);
        $this->assertTrue(rmdir($dir->flysystem));
        $this->assertDirectoryDoesNotExist($dir->local);
        $this->assertTrue(rmdir($dir->flysystem));
    }

    public function testWithContent(): void
    {
        $dir = $this->testDir->createDirectory(true);
        $dir->createFile(true);

        $this->assertDirectoryExists($dir->local);

        $this->assertFalse(@rmdir($dir->flysystem));
        $this->assertDirectoryExists($dir->local);

        $this->expectErrorWithMessage('Directory not empty');
        rmdir($dir->flysystem);
    }

    public function testRoot(): void
    {
        $this->assertFalse(@rmdir($this->testDir->flysystem));
        $this->assertFalse(@rmdir($this->testDir->flysystem.'/'));
        $this->assertDirectoryExists($this->testDir->local);

        $this->expectErrorWithMessage('Directory is root');
        rmdir($this->testDir->flysystem);
    }
}
