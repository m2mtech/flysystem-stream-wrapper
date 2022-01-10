<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use League\Flysystem\DirectoryAttributes;
use League\Flysystem\DirectoryListing;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemException;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\DirOpendirCommand;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\DirReaddirCommand;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\DirRewinddirCommand;
use PHPUnit\Framework\MockObject\MockObject;

class DirRewinddirTest extends AbstractStreamCommandTest
{
    public function test(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->method('listContents')
            ->willReturn(new DirectoryListing([
                new FileAttributes('one'),
                new FileAttributes('two'),
                new DirectoryAttributes('dir'),
            ]));
        $filesystem->expects($this->exactly(2))
            ->method('listContents')
            ->with('test');

        try {
            DirOpendirCommand::getDir($current);
        } catch (FilesystemException $e) {
            $this->fail();
        }

        $this->assertSame('one', DirReaddirCommand::run($current));
        $this->assertSame('two', DirReaddirCommand::run($current));
        $this->assertTrue(DirRewinddirCommand::run($current));
        $this->assertSame('one', DirReaddirCommand::run($current));
    }
}
