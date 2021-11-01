<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

use M2MTech\FlysystemStreamWrapper\Tests\Assert;

class OpendirTest extends AbstractFileCommandTest
{
    use Assert;

    public function test(): void
    {
        $dir = $this->testDir->createDirectory(true);
        $this->assertIsResource(opendir($dir->flysystem));
    }

    public function testNoDirectory(): void
    {
        $dir = $this->testDir->createDirectory();
        $this->expectError();
        $this->expectErrorMessageMatches('/Failed to open dir/i');
        opendir($dir->flysystem);
    }

    public function testFile(): void
    {
        $dir = $this->testDir->createFile(true);
        $this->expectError();
        $this->expectErrorMessageMatches('/Failed to open dir/i');
        opendir($dir->flysystem);
    }
}
