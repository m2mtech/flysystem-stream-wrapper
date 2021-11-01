<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class StreamSetReadBufferTest extends AbstractFileCommandTest
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
        $handle = fopen($file->flysystem, $mode);
        if (!is_resource($handle)) {
            $this->fail();
        }

        $this->assertSame(0, stream_set_read_buffer($handle, 1024));
        $this->assertSame(0, stream_set_read_buffer($handle, 0));

        fclose($handle);
    }
}
