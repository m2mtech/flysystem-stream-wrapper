<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests;

use M2MTech\FlysystemStreamWrapper\Tests\FileCommand\AbstractFileCommandTest;

class ReadTest extends AbstractFileCommandTest
{
    use Assert;

    public function test(): void
    {
        $file = $this->testDir->createFile(true);
        $handle = fopen($file->flysystem, 'rb');
        if (!is_resource($handle)) {
            $this->fail();
        }

        $this->assertIsString(fread($handle, 100));

        $this->assertSame(0, @fwrite($handle, 'more content'));
        $this->assertFalse(ftruncate($handle, 0));

        if (version_compare(PHP_VERSION, '7.4.0') < 0) {
            return;
        }

        $this->expectErrorWithMessage('Bad file descriptor', E_NOTICE);
        fwrite($handle, 'more content');
    }

    public function testCopyOnWrite(): void
    {
        $file = $this->testDir->createFile(true);
        $content = file_get_contents($file->local);
        $handle = fopen($file->flysystem, 'r+b');
        if (!is_resource($handle)) {
            $this->fail();
        }

        $this->assertSame(0, fseek($handle, 0, SEEK_END));
        $this->assertSame(13, fwrite($handle, ' more content'));
        $this->assertTrue(fflush($handle));

        $this->assertSame($content.' more content', file_get_contents($file->local));

        fclose($handle);
    }

    public function testCopyOnTruncate(): void
    {
        $file = $this->testDir->createFile(true);
        $content = (string) file_get_contents($file->local);
        $handle = fopen($file->flysystem, 'r+b');
        if (!is_resource($handle)) {
            $this->fail();
        }

        $this->assertTrue(ftruncate($handle, 4));
        $this->assertTrue(fflush($handle));

        $this->assertSame(substr($content, 0, 4), file_get_contents($file->local));

        fclose($handle);
    }
}
