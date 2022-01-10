<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class FtruncateTest extends AbstractFileCommandTest
{
    use DataProvider;

    /**
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

        $this->assertTrue(ftruncate($handle, 42));
        fclose($handle);

        $this->assertSame(42, filesize($file->local));
    }
}
