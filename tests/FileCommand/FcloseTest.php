<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

use M2MTech\FlysystemStreamWrapper\Tests\Assert;

class FcloseTest extends AbstractFileCommandTest
{
    use Assert;
    use DataProvider;

    /**
     * @dataProvider writeOnlyModeProvider
     * @dataProvider exclusiveWriteOnlyModeProvider
     * @dataProvider readWriteModeProvider
     * @dataProvider exclusiveReadWriteModeProvider
     */
    public function test(string $mode): void
    {
        $file = $this->testDir->createFile();
        $handle = fopen($file->flysystem, $mode);
        if (!is_resource($handle)) {
            $this->fail();
        }

        $this->assertTrue(fclose($handle));
    }

    /**
     * @dataProvider writeOnlyModeProvider
     * @dataProvider exclusiveWriteOnlyModeProvider
     * @dataProvider readWriteModeProvider
     * @dataProvider exclusiveReadWriteModeProvider
     */
    public function testFailed(string $mode): void
    {
        $file = $this->testDir->createFile();
        $handle = fopen($file->flysystem, $mode);
        if (!is_resource($handle)) {
            $this->fail();
        }

        fclose($handle);
        $this->assertIsClosedResource($handle);

        if (version_compare(PHP_VERSION, '8.0.0') >= 0) {
            return;
        }

        $this->assertFalse(@fclose($handle));

        $this->expectError();
        $this->expectErrorMessage('not a valid stream resource');
        fclose($handle);
    }
}
