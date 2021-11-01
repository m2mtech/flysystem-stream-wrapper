<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class CopyTest extends AbstractFileCommandTest
{
    public function test(): void
    {
        $file = $this->testDir->createFile(true);

        $bak = '.local.to.flysystem.bak';
        $this->assertTrue(copy($file->local, $file->flysystem.$bak));
        $this->assertFileExists($file->local.$bak);
        $this->assertFileEquals($file->local, $file->local.$bak);

        $this->assertTrue(copy($file->local, $file->flysystem.$bak));
        $this->assertFileEquals($file->local, $file->local.$bak);

        $bak = '.flysystem.to.local.bak';
        $this->assertTrue(copy($file->flysystem, $file->local.$bak));
        $this->assertFileExists($file->local.$bak);
        $this->assertFileEquals($file->local, $file->local.$bak);

        $bak = '.flysystem.to.flysystem.bak';
        $this->assertTrue(copy($file->flysystem, $file->flysystem.$bak));
        $this->assertFileExists($file->local.$bak);
        $this->assertFileEquals($file->local, $file->local.$bak);

        $this->assertTrue(copy($file->flysystem, $file->flysystem.$bak));
        $this->assertFileEquals($file->local, $file->local.$bak);
    }
}
