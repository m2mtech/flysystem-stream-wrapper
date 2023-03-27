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

class RenameTest extends AbstractFileCommandTestCase
{
    use Assert;

    public function test(): void
    {
        $old = $this->testDir->createFile(true);
        $new = $this->testDir->createFile();
        $this->assertFileDoesNotExist($new->local);
        $this->assertTrue(rename($old->flysystem, $new->flysystem));
        $this->assertFileDoesNotExist($old->local);
        $this->assertFileExists($new->local);
    }

    public function testOverwrite(): void
    {
        $file = $this->testDir->createFile(true);

        $overwrite = $this->testDir->createFile();
        file_put_contents($overwrite->local, 'overwritten');
        $this->assertFileExists($overwrite->local);

        $this->assertTrue(rename($overwrite->flysystem, $file->flysystem));
        $this->assertFileDoesNotExist($overwrite->local);
        $this->assertStringEqualsFile($file->local, 'overwritten');
    }

    public function testSameFile(): void
    {
        $file = $this->testDir->createFile(true);

        $this->assertTrue(rename($file->flysystem, $file->flysystem));
        $this->assertFileExists($file->local);
    }

    public function testNotExistingFile(): void
    {
        $old = $this->testDir->createFile();
        $new = $this->testDir->createFile();

        $this->assertFalse(@rename($old->flysystem, $new->flysystem));

        $this->expectErrorWithMessage('No such file or directory');
        rename($old->flysystem, $new->flysystem);
    }

    public function testDirectory(): void
    {
        $old = $this->testDir->createDirectory(true);
        $new = $this->testDir->createDirectory();

        $this->assertDirectoryDoesNotExist($new->local);
        $this->assertTrue(rename($old->flysystem, $new->flysystem));
        $this->assertDirectoryDoesNotExist($old->local);
        $this->assertDirectoryExists($new->local);
    }

    public function testOverwriteDirectory(): void
    {
        $old = $this->testDir->createDirectory(true);
        $file = $old->createFile(true);

        $new = $this->testDir->createDirectory(true);

        $this->assertTrue(rename($old->flysystem, $new->flysystem));
        $this->assertDirectoryDoesNotExist($old->local);
        $this->assertDirectoryExists($new->local);
        $this->assertFileDoesNotExist($file->local);
        $this->assertFileExists(str_replace($old->local, $new->local, $file->local));
    }

    public function testDirectoryNotEmpty(): void
    {
        $old = $this->testDir->createDirectory(true);
        $new = $this->testDir->createDirectory(true);
        $new->createFile(true);

        $this->assertFalse(@rename($old->flysystem, $new->flysystem));

        $this->expectErrorWithMessage('Directory not empty');
        rename($old->flysystem, $new->flysystem);
    }

    public function testFileToDirectory(): void
    {
        $file = $this->testDir->createFile(true);
        $dir = $this->testDir->createDirectory(true);

        $this->assertFalse(@rename($file->flysystem, $dir->flysystem));

        $this->expectErrorWithMessage('Is a directory');
        rename($file->flysystem, $dir->flysystem);
    }

    public function testDirectoryToName(): void
    {
        $dir = $this->testDir->createDirectory(true);
        $file = $this->testDir->createFile(true);

        $this->assertFalse(@rename($dir->flysystem, $file->flysystem));

        $this->expectErrorWithMessage('Not a directory');
        rename($dir->flysystem, $file->flysystem);
    }
}
