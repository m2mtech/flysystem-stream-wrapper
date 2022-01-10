<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class FileGetContentsTest extends AbstractFileCommandTest
{
    public function test(): void
    {
        $file = $this->testDir->createFile(true);
        $content = (string) file_get_contents($file->flysystem);
        $this->assertStringEqualsFile($file->local, $content);

        $part = (string) file_get_contents($file->flysystem, true, null, 7, 42);
        $this->assertSame(substr($content, 7, 42), $part);
    }
}
