<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use M2MTech\FlysystemStreamWrapper\FlysystemStreamWrapper;
use PHPUnit\Framework\TestCase;

class ReadmeTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        @rmdir(sys_get_temp_dir().'/happy_thoughts');
    }

    public function tearDown(): void
    {
        @rmdir(sys_get_temp_dir().'/happy_thoughts');

        parent::tearDown();
    }

    public function test(): void
    {
        $filesystem = new Filesystem(new LocalFilesystemAdapter(sys_get_temp_dir()));

        $this->assertTrue(FlysystemStreamWrapper::register('fly', $filesystem));

        $this->assertSame(11, file_put_contents('fly://filename.txt', 'someContent'));
        $this->assertTrue(mkdir('fly://happy_thoughts'));

        $this->assertTrue(FlysystemStreamWrapper::unregister('fly'));
    }
}
