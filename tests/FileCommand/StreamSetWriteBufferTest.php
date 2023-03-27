<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class StreamSetWriteBufferTest extends AbstractFileCommandTestCase
{
    use DataProvider;

    /**
     * @dataProvider readOnlyModeProvider
     * @dataProvider writeOnlyModeProvider
     * @dataProvider readWriteModeProvider
     */
    public function test(string $mode): void
    {
        $file = $this->testDir->createFile(true);
        $localHandle = fopen($file->local, $mode);
        $flysystemHandle = fopen($file->flysystem, $mode);
        if (!is_resource($localHandle) || !is_resource($flysystemHandle)) {
            $this->fail();
        }

        $this->assertSame(
            stream_set_read_buffer($localHandle, 1024),
            stream_set_read_buffer($flysystemHandle, 30)
        );
        $this->assertSame(
            stream_set_read_buffer($localHandle, 0),
            stream_set_read_buffer($flysystemHandle, 0)
        );

        fclose($localHandle);
        fclose($flysystemHandle);
    }

    public function testTriggerFlush(): void
    {
        $file = $this->testDir->createFile();
        $handle = fopen($file->flysystem, 'w+b');
        if (!is_resource($handle)) {
            $this->fail();
        }

        $this->assertSame(0, stream_set_write_buffer($handle, 1));
        $this->assertSame(2, fwrite($handle, 'aa'));
        $this->assertStringEqualsFile($file->local, 'aa');
        fclose($handle);
    }
}
