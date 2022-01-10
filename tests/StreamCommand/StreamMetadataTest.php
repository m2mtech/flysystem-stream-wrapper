<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\Visibility;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\StreamMetadataCommand;
use PHPUnit\Framework\MockObject\MockObject;

class StreamMetadataTest extends AbstractStreamCommandTest
{
    public function test(): void
    {
        $current = $this->getCurrent();

        $this->assertTrue(
            StreamMetadataCommand::run($current, self::TEST_PATH, STREAM_META_TOUCH, 'ignored')
        );
    }

    public function testMetaAccess(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->exactly(4))
            ->method('setVisibility')
            ->withConsecutive(
                ['test', Visibility::PRIVATE],
                ['test', Visibility::PUBLIC]
            );

        $this->assertTrue(
            StreamMetadataCommand::run($current, self::TEST_PATH, STREAM_META_ACCESS, 0600)
        );
        $this->assertTrue(
            StreamMetadataCommand::run($current, self::TEST_PATH, STREAM_META_ACCESS, 0644)
        );

        $filesystem->method('visibility')->willReturn('public');
        $filesystem->method('mimeType')->willReturn('directory');
        $filesystem->method('setVisibility')
            ->withConsecutive(
                ['test', Visibility::PRIVATE],
                ['test', Visibility::PUBLIC]
            );

        $this->assertTrue(
            StreamMetadataCommand::run($current, self::TEST_PATH, STREAM_META_ACCESS, 0700)
        );
        $this->assertTrue(
            StreamMetadataCommand::run($current, self::TEST_PATH, STREAM_META_ACCESS, 0755)
        );
    }

    public function testMetaAccessFail(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->method('setVisibility')
            ->willThrowException(UnableToSetVisibility::atLocation($current->path));

        $this->assertFalse(
            @StreamMetadataCommand::run($current, self::TEST_PATH, STREAM_META_ACCESS, 0600)
        );

        $this->expectError();
        $this->expectErrorMessage('Unable to change permissions');
        StreamMetadataCommand::run($current, self::TEST_PATH, STREAM_META_ACCESS, 0600);
    }

    public function testMetaTouch(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->once())
            ->method('write')
            ->with('test', '');

        $this->assertTrue(
            StreamMetadataCommand::run($current, self::TEST_PATH, STREAM_META_TOUCH, 'ignored')
        );

        $filesystem->method('fileExists')->willReturn(true);

        $this->assertTrue(
            StreamMetadataCommand::run($current, self::TEST_PATH, STREAM_META_TOUCH, 'ignored')
        );
    }

    public function testMetaTouchFail(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->method('write')
            ->willThrowException(UnableToWriteFile::atLocation($current->path));

        $this->assertFalse(
            @StreamMetadataCommand::run($current, self::TEST_PATH, STREAM_META_TOUCH, 'ignored')
        );

        $this->expectError();
        $this->expectErrorMessage('Unable to write to file');
        StreamMetadataCommand::run($current, self::TEST_PATH, STREAM_META_TOUCH, 'ignored');
    }

    /** @return array<array<int>> */
    public function metaOptionProvider(): array
    {
        return [
            [STREAM_META_GROUP],
            [STREAM_META_GROUP_NAME],
            [STREAM_META_OWNER],
            [STREAM_META_OWNER_NAME],
        ];
    }

    /** @dataProvider metaOptionProvider */
    public function testNotAvailableMetaOption(int $option): void
    {
        $current = $this->getCurrent();

        $this->assertFalse(StreamMetadataCommand::run($current, self::TEST_PATH, $option, 'ignored'));
    }
}
