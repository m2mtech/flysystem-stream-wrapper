<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\Issues;

use M2MTech\FlysystemStreamWrapper\Tests\Assert;
use M2MTech\FlysystemStreamWrapper\Tests\FileCommand\AbstractFileCommandTest;

class IsWritableDirectoryTest extends AbstractFileCommandTest
{
    use Assert;

    public function test(): void
    {
        $dir = $this->testDir->createDirectory();
        //mkdir($dir->flysystem, 0666, true);
        mkdir($dir->flysystem);
        $this->assertTrue(is_writable($dir->flysystem));
    }
}
