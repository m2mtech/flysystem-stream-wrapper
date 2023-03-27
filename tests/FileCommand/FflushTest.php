<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

use Faker\Factory as Faker;
use M2MTech\FlysystemStreamWrapper\Tests\Assert;

class FflushTest extends AbstractFileCommandTestCase
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
        $faker = Faker::create();

        $file = $this->testDir->createFile();
        $handle = fopen($file->flysystem, $mode);
        if (!is_resource($handle)) {
            $this->fail();
        }

        $content = $faker->word();
        fwrite($handle, $content);
        $this->assertFileDoesNotExist($file->local);

        $this->assertTrue(fflush($handle));
        $this->assertFileExists($file->local);
        $this->assertStringEqualsFile($file->local, $content);

        fclose($handle);
    }
}
