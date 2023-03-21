<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class RewinddirTest extends AbstractFileCommandTest
{
    public function test(): void
    {
        $file1 = $this->testDir->createFile(true);
        $file2 = $this->testDir->createFile(true);
        $dir = $this->testDir->createDirectory(true);

        $handle = opendir($this->testDir->flysystem);
        if (!is_resource($handle)) {
            $this->fail();
        }

        $dirContent = [];
        $i = 0;
        while (false !== ($entry = readdir($handle))) {
            $dirContent[] = $entry;
            if (0 === $i) {
                rewinddir($handle);
            }
            ++$i;
        }
        $this->assertCount(4, $dirContent);

        rewinddir();
        while (false !== ($entry = readdir($handle))) {
            $dirContent[] = $entry;
        }
        $this->assertCount(7, $dirContent);

        $this->assertContains($file1->name, $dirContent);
        $this->assertContains($file2->name, $dirContent);
        $this->assertContains($dir->name, $dirContent);

        $count = array_count_values($dirContent);
        $this->assertCount(3, $count);
        $this->assertContains(2, $count);
        $this->assertContains(3, $count);

        closedir($handle);
    }
}
