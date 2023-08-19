<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\Issues;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use M2MTech\FlysystemStreamWrapper\FlysystemStreamWrapper;
use M2MTech\FlysystemStreamWrapper\Tests\FileCommand\AbstractFileCommandTestCase;
use M2MTech\FlysystemStreamWrapper\Tests\FileCommand\DataProvider;
use M2MTech\FlysystemStreamWrapper\Tests\Filesystem\TestRootDirectory;

class EmulateDirectoryLastModifiedTest extends AbstractFileCommandTestCase
{
    use DataProvider;

    public function test(): void
    {
        FlysystemStreamWrapper::unregisterAll();
        $filesystem = new Filesystem(new LocalFilesystemAdapter($this->testDir->local));
        FlysystemStreamWrapper::register(TestRootDirectory::FLYSYSTEM, $filesystem, [
            FlysystemStreamWrapper::EMULATE_DIRECTORY_LAST_MODIFIED => true,
        ]);

        $old = time() - 100;
        $expected = time() - 10;
        $dir = $this->testDir->createDirectory(true);
        $file1 = $dir->createFile(true);
        touch($file1->local, $old);
        $file2 = $dir->createFile(true);
        touch($file2->local, $expected);
        $file3 = $dir->createFile(true);
        touch($file3->local, $old);

        $actual = filemtime($dir->flysystem);
        $this->assertSame($expected, $actual);
    }
}
