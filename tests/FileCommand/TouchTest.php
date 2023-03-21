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
use League\Flysystem\UnableToWriteFile;
use M2MTech\FlysystemStreamWrapper\FlysystemStreamWrapper;
use M2MTech\FlysystemStreamWrapper\Tests\Assert;

class TouchTest extends AbstractFileCommandTest
{
    use Assert;

    public function test(): void
    {
        $file = $this->testDir->createFile();
        $this->assertFileDoesNotExist($file->local);
        $this->assertTrue(touch($file->flysystem));
        $this->assertFileExists($file->local);
    }

    public function testExistingFile(): void
    {
        $file = $this->testDir->createFile(true);
        $content = file_get_contents($file->local);
        $this->assertTrue(touch($file->flysystem, time()));
        $this->assertSame($content, file_get_contents($file->local));
    }

    public function testFailed(): void
    {
        $filesystem = $this->createStub(FilesystemOperator::class);
        $filesystem->method('write')->willThrowException(new UnableToWriteFile());

        FlysystemStreamWrapper::register('fail', $filesystem);
        $this->assertFalse(@touch('fail://path'));

        $this->expectErrorWithMessage('Unable to write to file');
        touch('fail://path');
    }
}
