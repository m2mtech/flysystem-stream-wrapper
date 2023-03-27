<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use League\Flysystem\Visibility;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\UrlStatCommand;
use M2MTech\FlysystemStreamWrapper\Tests\Assert;
use PHPUnit\Framework\MockObject\MockObject;

class UrlStatTest extends AbstractStreamCommandTestCase
{
    use Assert;

    public function test(): void
    {
        $current = $this->getCurrent();
        /** @var MockObject $filesystem */
        $filesystem = $current->filesystem;
        $filesystem->expects($this->once())
            ->method('visibility')
            ->with('test')
            ->willReturn(Visibility::PUBLIC);

        $stats = UrlStatCommand::run($current, self::TEST_PATH, 0);
        $this->assertIsArray($stats);
    }

    public function testFailed(): void
    {
        $current = $this->getCurrent();

        $this->assertFalse(@UrlStatCommand::run($current, self::TEST_PATH, 0));

        $this->expectErrorWithMessage('Stat failed');
        UrlStatCommand::run($current, self::TEST_PATH, 0);
    }

    public function testSuppressErrorMessage(): void
    {
        $current = $this->getCurrent();

        $this->assertFalse(UrlStatCommand::run($current, self::TEST_PATH, STREAM_URL_STAT_QUIET));
    }
}
