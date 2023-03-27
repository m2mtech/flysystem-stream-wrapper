<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use M2MTech\FlysystemStreamWrapper\FlysystemStreamWrapper;
use M2MTech\FlysystemStreamWrapper\Tests\Filesystem\TestDirectory;
use M2MTech\FlysystemStreamWrapper\Tests\Filesystem\TestRootDirectory;
use PHPUnit\Framework\TestCase;

abstract class AbstractFileCommandTestCase extends TestCase
{
    /** @var TestDirectory */
    protected $testDir;

    public function setUp(): void
    {
        parent::setUp();

        $this->testDir = new TestRootDirectory();
        mkdir($this->testDir->local);
        $filesystem = new Filesystem(new LocalFilesystemAdapter($this->testDir->local));
        FlysystemStreamWrapper::register(TestRootDirectory::FLYSYSTEM, $filesystem);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        FlysystemStreamWrapper::unregisterAll();
        $this->testDir->removeLocalDirectory();
    }
}
