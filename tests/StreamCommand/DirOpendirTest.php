<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use Generator;
use League\Flysystem\FilesystemException;
use League\Flysystem\SymbolicLinkEncountered;
use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\DirOpendirCommand;
use PHPUnit\Framework\MockObject\MockObject;

class DirOpendirTest extends AbstractStreamCommandTest
{
    public function test(): void
    {
        $this->getFilesystem([
            'visibility' => 'public',
            'mimeType' => 'directory',
        ]);

        $this->assertTrue(DirOpendirCommand::run(
            new FileData(),
            self::TEST_PATH,
            42
        ));
    }

    public function testFile(): void
    {
        $this->getFilesystem([
            'visibility' => 'public',
            'mimeType' => 'file',
        ]);

        $this->assertFalse(DirOpendirCommand::run(
            new FileData(),
            self::TEST_PATH,
            42
        ));
    }

    public function testBrockenGetDir(): void
    {
        /** @var MockObject $filesystem */
        $filesystem = $this->getFilesystem();
        $filesystem->method('listContents')
            ->willThrowException(SymbolicLinkEncountered::atLocation(self::TEST_PATH));

        $this->expectError();
        $this->expectErrorMessage('Unsupported symbolic link encountered');
        $this->expectErrorMessageMatches('/Failed to open dir/i');
        DirOpendirCommand::run(new FileData(), self::TEST_PATH, 42);
    }

    public function testGetDir(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->once())
            ->method('listContents')
            ->with('test');

        $this->assertNull($current->dirListing);
        try {
            DirOpendirCommand::getDir($current);
        } catch (FilesystemException $e) {
            $this->fail();
        }
        $this->assertInstanceOf(Generator::class, $current->dirListing);
    }
}
