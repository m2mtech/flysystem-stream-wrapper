<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class StreamSetBlockingTest extends AbstractFileCommandTestCase
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

        $this->assertTrue(stream_set_blocking($handle, true));
        $this->assertTrue(stream_set_blocking($handle, false));

        fclose($handle);
    }
}
