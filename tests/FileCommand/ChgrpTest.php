<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

use Faker\Factory as Faker;

class ChgrpTest extends AbstractFileCommandTest
{
    public function test(): void
    {
        $faker = Faker::create();

        $this->assertFalse(chgrp($this->testDir->flysystem, $faker->word()));
    }
}
