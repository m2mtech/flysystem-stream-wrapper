<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class FgetcTest extends AbstractFileCommandTest
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

        $byte = fgetc($handle);
        if (!in_array($mode, ['a+', 'w+'])) {
            $this->assertIsString($byte);
            if ('php' === pathinfo($file->local, PATHINFO_EXTENSION)) {
                $this->assertSame('<', $byte);
            }
        } else {
            $this->assertFalse($byte);
        }

        fclose($handle);
    }
}
