<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

use Faker\Factory as Faker;

class FilePutContentsTest extends AbstractFileCommandTest
{
    public function test(): void
    {
        $faker = Faker::create();

        $file = $this->testDir->createFile();
        $content = $faker->paragraphs(10, true);
        if (is_array($content)) {
            $content = implode($content);
        }
        $size = file_put_contents($file->flysystem, $content);
        $this->assertGreaterThan(10, $size);
        $this->assertStringEqualsFile($file->local, $content);
    }
}
