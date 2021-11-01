<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\Filesystem;

use Faker\Generator as Faker;

abstract class AbstractTestObject
{
    /** @var TestDirectory */
    public $parentDirectory;

    /** @var string */
    public $name;

    /** @var string */
    public $local;

    /** @var string */
    public $flysystem;

    /** @var string */
    public $flysystemSeparator;

    /** @var Faker */
    public $faker;

    public function __construct(TestDirectory $parent = null, string $flysystemSeparator = '/')
    {
        $this->flysystemSeparator = $flysystemSeparator;

        if (!$parent) {
            return;
        }

        $this->faker = $parent->faker;
        $this->name = $this->faker->unique()->word();

        $this->parentDirectory = $parent;
        $this->local = $parent->local.DIRECTORY_SEPARATOR.$this->name;
        $this->flysystem = $parent->flysystem.$flysystemSeparator.$this->name;
    }
}
