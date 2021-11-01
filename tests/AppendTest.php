<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests;

use M2MTech\FlysystemStreamWrapper\Tests\FileCommand\AbstractFileCommandTest;

class AppendTest extends AbstractFileCommandTest
{
    public function test(): void
    {
        $file = $this->testDir->createFile(true);
        $content = file_get_contents($file->local);

        $handle = fopen($file->flysystem, 'ab');
        if (!is_resource($handle)) {
            $this->fail();
        }

        $this->assertSame(0, ftell($handle));
        $this->assertSame(0, fseek($handle, 0));

        // Test write-only.
        $this->assertSame('', fread($handle, 100));

        $this->assertSame(5, fwrite($handle, '12345'));
        fclose($handle);

        $this->assertStringEqualsFile($file->local, $content.'12345');

        $handle = fopen($file->flysystem, 'c+b');
        if (!is_resource($handle)) {
            $this->fail();
        }

        $this->assertSame(0, ftell($handle));
        $this->assertSame($content.'12345', fread($handle, 102400));
        fclose($handle);
    }

    public function testNewFile(): void
    {
        $file = $this->testDir->createFile();
        $handle = fopen($file->flysystem, 'ab');
        if (!is_resource($handle)) {
            $this->fail();
        }

        $this->assertSame(5, fwrite($handle, '12345'));
        fclose($handle);

        $this->assertStringEqualsFile($file->local, '12345');
    }
}
