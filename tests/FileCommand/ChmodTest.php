<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToSetVisibility;
use M2MTech\FlysystemStreamWrapper\FlysystemStreamWrapper;
use M2MTech\FlysystemStreamWrapper\Tests\Assert;

class ChmodTest extends AbstractFileCommandTest
{
    use Assert;

    public function test(): void
    {
        $dir = $this->testDir;
        $this->assertPermission($dir->local, 0755);
        $this->assertTrue(chmod($dir->flysystem, 0700));
        $this->assertPermission($dir->local, 0700);
        $this->assertTrue(chmod($dir->flysystem, 0777));
        $this->assertPermission($dir->local, 0755);

        $file = $dir->createFile(true);
        $this->assertPermission($file->local, 0644);
        $this->assertTrue(chmod($file->flysystem, 0600));
        $this->assertPermission($file->local, 0600);
        $this->assertTrue(chmod($file->flysystem, 0777));
        $this->assertPermission($file->local, 0644);
    }

    public function testFailed(): void
    {
        $filesystem = $this->createStub(FilesystemOperator::class);
        $filesystem->method('setVisibility')->willThrowException(new UnableToSetVisibility());

        FlysystemStreamWrapper::register('fail', $filesystem);
        $this->assertFalse(@chmod('fail://path', 0777));

        $this->expectError();
        chmod('fail://path', 0777);
    }
}
