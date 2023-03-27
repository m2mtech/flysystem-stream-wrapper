<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\Visibility;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\StreamMetadataCommand;
use M2MTech\FlysystemStreamWrapper\Tests\Assert;
use PHPUnit\Framework\MockObject\MockObject;

class StreamMetadataTest extends AbstractStreamCommandTestCase
{
    use Assert;

    public function test(): void
    {
        $current = $this->getCurrent();

        $this->assertTrue(
            StreamMetadataCommand::run($current, self::TEST_PATH, STREAM_META_TOUCH, 'ignored')
        );
    }

    /** @return array<array<bool|int|string>> */
    public static function metaAccessProvider(): array
    {
        return [
            [false, Visibility::PRIVATE, 0600],
            [false, Visibility::PUBLIC, 0644],
            [true, Visibility::PRIVATE, 0600],
            [true, Visibility::PUBLIC, 0644],
        ];
    }

    /** @dataProvider metaAccessProvider */
    public function testMetaAccess(bool $isDir, string $visibility, int $permissions): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->once())
            ->method('setVisibility')
            ->with('test', $visibility);

        if ($isDir) {
            $filesystem->method('mimeType')->willReturn('directory');
        }

        $this->assertTrue(
            StreamMetadataCommand::run($current, self::TEST_PATH, STREAM_META_ACCESS, $permissions)
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

        $this->expectErrorWithMessage('Unable to change permissions');
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

        $this->expectErrorWithMessage('Unable to write to file');
        StreamMetadataCommand::run($current, self::TEST_PATH, STREAM_META_TOUCH, 'ignored');
    }

    /** @return array<array<int>> */
    public static function metaOptionProvider(): array
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
