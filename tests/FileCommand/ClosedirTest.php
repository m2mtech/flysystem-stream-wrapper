<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

use M2MTech\FlysystemStreamWrapper\Tests\Assert;

class ClosedirTest extends AbstractFileCommandTestCase
{
    use Assert;

    /** @ */
    public function test(): void
    {
        $dir = $this->testDir->createDirectory(true);
        $handle = opendir($dir->flysystem);
        if (!is_resource($handle)) {
            $this->fail();
        }

        closedir($handle);
        $this->assertIsClosedResource($handle);
    }

    public function testNoHandle(): void
    {
        $dir = $this->testDir->createDirectory(true);
        $handle = opendir($dir->flysystem);
        if (!is_resource($handle)) {
            $this->fail();
        }

        $this->assertTrue(rewind($handle));
        closedir();

        if (version_compare(PHP_VERSION, '8.0.0') < 0) {
            $this->assertFalse(@rewind($handle));
        }
    }
}
