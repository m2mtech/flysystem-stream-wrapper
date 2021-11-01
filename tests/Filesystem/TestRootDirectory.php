<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\Filesystem;

use Faker\Factory as Faker;

class TestRootDirectory extends TestDirectory
{
    public const TEST_DIR = 'testDir';
    public const FLYSYSTEM = 'flysystem';

    public function __construct(string $flysystemSeparator = '/')
    {
        parent::__construct(null, $flysystemSeparator);

        $this->local = sys_get_temp_dir().DIRECTORY_SEPARATOR.self::TEST_DIR;
        $this->flysystem = self::FLYSYSTEM.'://';

        $this->faker = Faker::create();

        $this->removeLocalDirectory();
    }
}
