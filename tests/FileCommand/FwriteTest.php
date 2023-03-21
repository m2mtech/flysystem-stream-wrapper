<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class FwriteTest extends AbstractFileCommandTest
{
    use DataProvider;

    /**
     * @dataProvider readWriteModeProvider
     * @dataProvider writeOnlyModeProvider
     */
    public function test(string $mode): void
    {
        $file = $this->testDir->createFile(true);
        $oldSize = filesize($file->local);

        $handle = fopen($file->flysystem, $mode);
        if (!is_resource($handle)) {
            $this->fail();
        }

        $this->assertSame(4, fwrite($handle, 'test'));

        fclose($handle);

        if ('w' === $mode[0]) {
            $this->assertSame(4, filesize($file->local));
        } elseif ('a' === $mode[0]) {
            $this->assertSame($oldSize + 4, filesize($file->local));
        } else {
            $this->assertSame($oldSize, filesize($file->local));
        }
    }
}
