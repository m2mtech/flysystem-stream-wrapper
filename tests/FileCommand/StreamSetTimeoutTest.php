<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class StreamSetTimeoutTest extends AbstractFileCommandTestCase
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
            stream_set_timeout($localHandle, 30),
            stream_set_timeout($flysystemHandle, 30)
        );

        fclose($localHandle);
        fclose($flysystemHandle);
    }
}
