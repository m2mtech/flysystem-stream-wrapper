<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use League\Flysystem\Config;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\Visibility;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\MkdirCommand;
use PHPUnit\Framework\MockObject\MockObject;

class MkdirTest extends AbstractStreamCommandTest
{
    public function test(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->once())
            ->method('createDirectory')
            ->with(
                'test',
                [
                    Config::OPTION_VISIBILITY => Visibility::PUBLIC,
                ]
            );

        $this->assertTrue(MkdirCommand::run($current, self::TEST_PATH, 0777, 42));
    }

    public function testDirectoryExists(): void
    {
        $current = $this->getCurrent([
            'visibility' => 'public',
            'mimeType' => 'dontCare',
        ]);

        $this->assertFalse(@MkdirCommand::run($current, self::TEST_PATH, 0777, 42));

        $this->expectError();
        $this->expectErrorMessage('Directory exists');
        MkdirCommand::run($current, self::TEST_PATH, 0777, 42);
    }

    public function testPermission(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->exactly(2))
            ->method('createDirectory')
            ->withConsecutive(
                [
                    'test',
                    [
                        Config::OPTION_VISIBILITY => Visibility::PUBLIC,
                    ],
                ],
                [
                    'test',
                    [
                        Config::OPTION_VISIBILITY => Visibility::PRIVATE,
                    ],
                ]
            );

        $this->assertTrue(MkdirCommand::run($current, self::TEST_PATH, 0777, 42));
        $this->assertTrue(MkdirCommand::run($current, self::TEST_PATH, 0700, 42));
    }

    public function testFailed(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->method('createDirectory')
            ->willThrowException(UnableToCreateDirectory::atLocation(self::TEST_PATH));

        $this->assertFalse(@MkdirCommand::run($current, self::TEST_PATH, 0777, 42));

        $this->expectError();
        $this->expectErrorMessage('Cannot create directory');
        MkdirCommand::run($current, self::TEST_PATH, 0777, 42);
    }
}
