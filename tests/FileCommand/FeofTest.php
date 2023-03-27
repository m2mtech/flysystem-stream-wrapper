<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class FeofTest extends AbstractFileCommandTestCase
{
    use DataProvider;

    /**
     * @dataProvider readWriteModeProvider
     * @dataProvider exclusiveReadWriteModeProvider
     */
    public function testReadWritable(string $mode): void
    {
        $file = $this->testDir->createFile();
        $handle = fopen($file->flysystem, $mode);
        if (!is_resource($handle)) {
            $this->fail();
        }

        $this->assertFalse(feof($handle));
        fgetc($handle);
        $this->assertTrue(feof($handle));
        fclose($handle);
    }

    /**
     * @dataProvider readOnlyModeProvider
     * @dataProvider readWriteModeProvider
     */
    public function testReadable(string $mode): void
    {
        $file = $this->testDir->createFile(true);
        $handle = fopen($file->flysystem, $mode);
        if (!is_resource($handle)) {
            $this->fail();
        }

        $this->assertFalse(feof($handle));
        fgetc($handle);
        if (!in_array($mode, ['a+', 'w+'])) {
            $this->assertFalse(feof($handle));
        } else {
            $this->assertTrue(feof($handle));
        }
        while (!feof($handle)) {
            fread($handle, 1024);
        }
        $this->assertTrue(feof($handle));
        fclose($handle);
    }
}
