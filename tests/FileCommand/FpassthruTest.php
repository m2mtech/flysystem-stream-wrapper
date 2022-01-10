<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class FpassthruTest extends AbstractFileCommandTest
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

        $content = (string) file_get_contents($file->local);
        if (!in_array($mode, ['a+', 'w+'])) {
            $this->expectOutputString($content);
            $size = fpassthru($handle);
            $this->assertSame(strlen($content), $size);
        } else {
            $this->expectOutputString('');
            $size = fpassthru($handle);
            $this->assertSame(0, $size);
        }

        fclose($handle);
    }
}
