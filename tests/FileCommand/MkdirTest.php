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

class MkdirTest extends AbstractFileCommandTest
{
    use Assert;

    public function test(): void
    {
        $dir = $this->testDir->createDirectory();
        $this->assertDirectoryDoesNotExist($dir->local);
        $this->assertTrue(mkdir($dir->flysystem));
        $this->assertDirectoryExists($dir->local);
        $this->assertPermission($dir->local, 0755);
    }

    public function testMultiLevel(): void
    {
        $parent = $this->testDir->createDirectory();
        $dir = $parent->createDirectory();
        $this->assertDirectoryDoesNotExist($dir->local);
        $this->assertTrue(mkdir($dir->flysystem));
        $this->assertDirectoryExists($dir->local);
        $this->assertPermission($parent->local, 0755);
        $this->assertPermission($dir->local, 0755);
    }

    public function testPermission(): void
    {
        $parent = $this->testDir->createDirectory();
        $this->assertTrue(mkdir($parent->flysystem, 0755));
        $dir = $parent->createDirectory();
        $this->assertTrue(mkdir($dir->flysystem, 0700));
        $this->assertPermission($parent->local, 0755);
        $this->assertPermission($dir->local, 0700);
    }

    public function testFailed(): void
    {
        $dir = $this->testDir->createDirectory(true);
        $this->assertFalse(@mkdir($dir->flysystem));

        $this->expectErrorWithMessage('Directory exists');
        mkdir($dir->flysystem);
    }
}
