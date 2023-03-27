<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use League\Flysystem\Config;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\Visibility;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\MkdirCommand;
use M2MTech\FlysystemStreamWrapper\Tests\Assert;
use PHPUnit\Framework\MockObject\MockObject;

class MkdirTest extends AbstractStreamCommandTestCase
{
    use Assert;

    public function test(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->once())
            ->method('createDirectory')
            ->with('test', [Config::OPTION_VISIBILITY => Visibility::PUBLIC]);

        $this->assertTrue(MkdirCommand::run($current, self::TEST_PATH, 0777, 42));
    }

    public function testDirectoryExists(): void
    {
        $current = $this->getCurrent([
            'visibility' => 'public',
            'mimeType' => 'dontCare', // V2
            'directoryExists' => true, // V3
        ]);

        $this->assertFalse(@MkdirCommand::run($current, self::TEST_PATH, 0777, 42));

        $this->expectErrorWithMessage('Directory exists');
        MkdirCommand::run($current, self::TEST_PATH, 0777, 42);
    }

    /** @return array<array<int|string>> */
    public static function permissionProvider(): array
    {
        return [
            [Visibility::PUBLIC, 0777],
            [Visibility::PRIVATE, 0700],
        ];
    }

    /** @dataProvider permissionProvider */
    public function testPermission(string $visibility, int $permission): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->once())
            ->method('createDirectory')
            ->with('test', [Config::OPTION_VISIBILITY => $visibility]);

        $this->assertTrue(MkdirCommand::run($current, self::TEST_PATH, $permission, 42));
    }

    public function testFailed(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->method('createDirectory')
            ->willThrowException(UnableToCreateDirectory::atLocation(self::TEST_PATH));

        $this->assertFalse(@MkdirCommand::run($current, self::TEST_PATH, 0777, 42));

        $this->expectErrorWithMessage('Cannot create directory');
        MkdirCommand::run($current, self::TEST_PATH, 0777, 42);
    }
}
