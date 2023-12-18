<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class RewindTest extends AbstractFileCommandTestCase
{
    use DataProvider;

    /**
     * @dataProvider readOnlyModeProvider
     * @dataProvider readWriteModeProvider
     * @dataProvider writeOnlyModeProvider
     */
    public function test(string $mode): void
    {
        $file = $this->testDir->createFile(true);
        $handle = fopen($file->flysystem, $mode);
        if (!is_resource($handle)) {
            $this->fail();
        }

        if ('a+' === $mode) {
            $this->assertSame(filesize($file->local), ftell($handle));
        } else {
            $this->assertSame(0, ftell($handle));
        }

        fseek($handle, 42);
        if ('w' === $mode[0]) {
            if (version_compare(PHP_VERSION, '8.3.0') >= 0) {
                $this->assertSame(42, ftell($handle));
            } else {
                $this->assertSame(0, ftell($handle));
            }
        } elseif ('a' === $mode) {
            $this->assertSame(0, ftell($handle));
        } else {
            $this->assertSame(42, ftell($handle));
        }

        $this->assertTrue(rewind($handle));
        $this->assertSame(0, ftell($handle));

        fclose($handle);
    }
}
