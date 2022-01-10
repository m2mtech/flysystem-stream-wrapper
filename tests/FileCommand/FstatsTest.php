<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class FstatsTest extends AbstractStatTest
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

        $expected = $this->expectedStats($file->local, 0644);
        if ('w' === $mode[0]) {
            $expected[7] = $expected['size'] = 0;
        }
        $this->assertEquals($expected, fstat($handle));

        fclose($handle);
    }

    public function testNewNonExistentRemoteFile(): void
    {
        $file = $this->testDir->createFile(true);
        $handle = fopen($file->flysystem, 'wb');
        if (!is_resource($handle)) {
            $this->fail();
        }

        fwrite($handle, '1');

        $stats = fstat($handle);
        if (!$stats) {
            $this->fail();
        }

        $this->assertSame(1, $stats['size']);

        fclose($handle);
    }
}
