<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

class FlockTest extends AbstractFileCommandTest
{
    use DataProvider;

    /**
     * @dataProvider writeOnlyModeProvider
     * @dataProvider readWriteModeProvider
     */
    public function test(string $mode): void
    {
        $file = $this->testDir->createFile(true);
        $handle = fopen($file->flysystem, $mode);
        if (!is_resource($handle)) {
            $this->fail();
        }
        $this->assertTrue(flock($handle, LOCK_EX));
        $this->assertTrue(flock($handle, LOCK_EX));

        $blockedHandle = fopen($file->flysystem, $mode);
        if (!is_resource($blockedHandle)) {
            $this->fail();
        }
        $this->assertFalse(flock($blockedHandle, LOCK_EX | LOCK_NB));

        $this->assertTrue(flock($handle, LOCK_UN));
        $this->assertTrue(flock($blockedHandle, LOCK_EX | LOCK_NB));

        fclose($handle);
        fclose($blockedHandle);
    }

    public function testSharedReading(): void
    {
        $file = $this->testDir->createFile(true);

        $reader = fopen($file->flysystem, 'rb');
        $readWriter = fopen($file->flysystem, 'rb+');
        $writer = fopen($file->flysystem, 'ab');
        if (!is_resource($reader) || !is_resource($readWriter) || !is_resource($writer)) {
            $this->fail();
        }

        $this->assertTrue(flock($reader, LOCK_SH));
        $this->assertTrue(flock($readWriter, LOCK_SH));
        $this->assertFalse(flock($writer, LOCK_EX | LOCK_NB));

        $this->assertTrue(flock($readWriter, LOCK_UN));
        $this->assertFalse(flock($readWriter, LOCK_EX | LOCK_NB));

        $this->assertTrue(flock($reader, LOCK_UN));
        $this->assertTrue(flock($writer, LOCK_EX));

        $this->assertFalse(flock($reader, LOCK_SH | LOCK_NB));
    }
}
