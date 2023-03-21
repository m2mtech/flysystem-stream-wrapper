<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class FgetsTest extends AbstractFileCommandTest
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

        rewind($handle);
        $line = (string) fgets($handle);
        rewind($handle);
        $char = (string) fgets($handle, 2);

        if ('w+' === $mode) {
            $this->assertEmpty($line);
            $this->assertEmpty($char);
        } else {
            $this->assertGreaterThan(1, strlen($line));
            $this->assertSame(1, strlen($char));
            $this->assertSame($char, $line[0]);
        }

        fclose($handle);
    }
}
