<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class ReaddirTest extends AbstractFileCommandTestCase
{
    use DataProvider;

    /**
     * @dataProvider trueFalseProvider
     */
    public function test(bool $useHandle): void
    {
        $dir = $this->testDir->createDirectory(true);
        $file1 = $dir->createFile(true);
        $file2 = $dir->createFile(true);
        $file3 = $dir->createFile(true);
        $subDir = $dir->createDirectory(true);

        $handle = opendir($dir->flysystem);
        if (!is_resource($handle)) {
            $this->fail();
        }

        $dirContent = [];
        if ($useHandle) {
            while (false !== ($entry = readdir($handle))) {
                $dirContent[] = $entry;
            }
        } else {
            while (false !== ($entry = readdir())) {
                $dirContent[] = $entry;
            }
        }

        $this->assertCount(4, $dirContent);
        $this->assertContains($file1->name, $dirContent);
        $this->assertContains($file2->name, $dirContent);
        $this->assertContains($file3->name, $dirContent);
        $this->assertContains($subDir->name, $dirContent);

        closedir($handle);
    }

    public function testEmpty(): void
    {
        $dir = $this->testDir->createDirectory(true);

        $handle = opendir($dir->flysystem);
        if (!is_resource($handle)) {
            $this->fail();
        }

        $dirContent = [];
        while (false !== ($entry = readdir($handle))) {
            $dirContent[] = $entry;
        }
        $this->assertCount(0, $dirContent);

        closedir($handle);
    }
}
