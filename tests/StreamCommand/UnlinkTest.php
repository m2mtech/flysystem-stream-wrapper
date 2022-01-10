<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use League\Flysystem\UnableToDeleteFile;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\UnlinkCommand;
use PHPUnit\Framework\MockObject\MockObject;

class UnlinkTest extends AbstractStreamCommandTest
{
    public function test(): void
    {
        $current = $this->getCurrent([
            'visibility' => 'public',
            'mimeType' => 'dontCare',
        ]);

        $this->assertTrue(UnlinkCommand::run($current, self::TEST_PATH));
    }

    public function testNotExisting(): void
    {
        $current = $this->getCurrent();

        $this->assertFalse(@UnlinkCommand::run($current, self::TEST_PATH));

        $this->expectError();
        $this->expectErrorMessage('No such file or directory');
        UnlinkCommand::run($current, self::TEST_PATH);
    }

    public function testFailed(): void
    {
        $current = $this->getCurrent([
            'visibility' => 'public',
            'mimeType' => 'dontCare',
        ]);
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->exactly(2))
            ->method('delete')
            ->with('test')
            ->willThrowException(UnableToDeleteFile::atLocation(self::TEST_PATH));

        $this->assertFalse(@UnlinkCommand::run($current, self::TEST_PATH));

        $this->expectError();
        $this->expectErrorMessage('Could not delete file');
        UnlinkCommand::run($current, self::TEST_PATH);
    }
}
