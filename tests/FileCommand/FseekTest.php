<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class FseekTest extends AbstractFileCommandTest
{
    use DataProvider;

    /**
     * @dataProvider readOnlyModeProvider
     * @dataProvider readWriteModeProvider
     */
    public function test(string $mode): void
    {
        $file = $this->testDir->createFile(true);
        $handle = fopen($file->flysystem, $mode);
        if (!is_resource($handle)) {
            $this->fail();
        }

        if ('w+' !== $mode) {
            $content = (string) file_get_contents($file->local);
            $this->assertSame(0, fseek($handle, 23));
            $string = (string) fread($handle, 42);
            $expected = (string) substr($content, 23, 42);
            $this->assertSame($expected, $string);

            $this->assertSame(0, fseek($handle, 23, SEEK_CUR));
            $string = (string) fread($handle, 42);
            $expected = (string) substr($content, 23 * 2 + 42, 42);
            $this->assertSame($expected, $string);

            $this->assertSame(0, fseek($handle, -23, SEEK_END));
            $string = (string) fread($handle, 42);
            $expected = (string) substr($content, -23);
            $this->assertSame($expected, $string);
        } else {
            $this->assertSame(-1, fseek($handle, 42));
            $this->assertSame(-1, fseek($handle, 42, SEEK_CUR));
            $this->assertSame(-1, fseek($handle, -42, SEEK_END));
        }

        fclose($handle);
    }
}
