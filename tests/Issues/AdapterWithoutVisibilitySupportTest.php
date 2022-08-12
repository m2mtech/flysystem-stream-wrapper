<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\Issues;

use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\Visibility;
use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\StreamMetadataCommand;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\StreamStatCommand;
use M2MTech\FlysystemStreamWrapper\FlysystemStreamWrapper;
use M2MTech\FlysystemStreamWrapper\Tests\Assert;
use M2MTech\FlysystemStreamWrapper\Tests\StreamCommand\AbstractStreamCommandTest;
use PHPUnit\Framework\MockObject\MockObject;
use TypeError;

class AdapterWithoutVisibilitySupportTest extends AbstractStreamCommandTest
{
    use Assert;

    public function testMetaAccess(): void
    {
        $current = $this->getCurrent();
        $this->ignoreVisibilityErrors($current);

        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->exactly(2))
            ->method('setVisibility')
            ->withConsecutive(
                ['test', Visibility::PRIVATE],
                ['test', Visibility::PUBLIC]
            )
            ->willThrowException(UnableToSetVisibility::atLocation($current->path))
        ;

        $this->assertTrue(
            StreamMetadataCommand::run($current, self::TEST_PATH, STREAM_META_ACCESS, 0600)
        );
        $this->assertTrue(
            StreamMetadataCommand::run($current, self::TEST_PATH, STREAM_META_ACCESS, 0644)
        );
    }

    public function testVisibilityForDirectory(): void
    {
        $current = $this->getCurrent();
        $this->ignoreVisibilityErrors($current);

        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->exactly(1))
            ->method('mimeType')
            ->with('test')
            ->willReturn('directory');
        $filesystem->expects($this->exactly(1))
            ->method('visibility')
            ->with('test')
            ->willThrowException(UnableToRetrieveMetadata::visibility($current->file));

        $stats = StreamStatCommand::getRemoteStats($current);
        $this->assertSame(040755, $stats[0]);
    }

    public function testVisibilityForFile(): void
    {
        $current = $this->getCurrent();
        $this->ignoreVisibilityErrors($current);

        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->exactly(1))
            ->method('visibility')
            ->with('test')
            ->willThrowException(UnableToRetrieveMetadata::visibility($current->file));

        $stats = StreamStatCommand::getRemoteStats($current);
        $this->assertSame(0100644, $stats[0]);
    }


    public function testVisibilityForDirectoryOnTypeError(): void
    {
        $current = $this->getCurrent();
        $this->ignoreVisibilityErrors($current);

        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->exactly(1))
            ->method('mimeType')
            ->with('test')
            ->willReturn('directory');
        $filesystem->expects($this->exactly(1))
            ->method('visibility')
            ->with('test')
            ->willThrowException(new TypeError('Return value must be of type string, null returned'));

        $stats = StreamStatCommand::getRemoteStats($current);
        $this->assertSame(040755, $stats[0]);
    }

    public function testVisibilityForFileOnTypeError(): void
    {
        $current = $this->getCurrent();
        $this->ignoreVisibilityErrors($current);

        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->exactly(1))
            ->method('visibility')
            ->with('test')
            ->willThrowException(new TypeError('Return value must be of type string, null returned'));

        $stats = StreamStatCommand::getRemoteStats($current);
        $this->assertSame(0100644, $stats[0]);
    }

    private function ignoreVisibilityErrors(FileData $current): void
    {
        FlysystemStreamWrapper::$config[AbstractStreamCommandTest::TEST_PROTOCOL][FlysystemStreamWrapper::IGNORE_VISIBILITY_ERRORS] = true;
        $current->config[FlysystemStreamWrapper::IGNORE_VISIBILITY_ERRORS] = true;
    }
}
