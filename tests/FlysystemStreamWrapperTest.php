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

class FlysystemStreamWrapperTest extends TestCase
{
    public function tearDown(): void
    {
        FlysystemStreamWrapper::unregisterAll();
    }

    public function testRegister(): void
    {
        $filesystem = new Filesystem(new LocalFilesystemAdapter(sys_get_temp_dir()));
        $this->assertTrue(FlysystemStreamWrapper::register('test', $filesystem));
        $this->assertContains('test', stream_get_wrappers());
        $this->assertTrue(stream_is_local('test://'));
        // Registering twice not possible
        $this->assertFalse(FlysystemStreamWrapper::register('test', $filesystem));
        $this->assertTrue(FlysystemStreamWrapper::unregister('test'));

        $this->assertTrue(FlysystemStreamWrapper::register('test', $filesystem, [], STREAM_IS_URL));
        $this->assertFalse(stream_is_local('test://'));
        $this->assertTrue(FlysystemStreamWrapper::unregister('test'));
        $this->assertFalse(FlysystemStreamWrapper::unregister('test'));
    }

    public function testGetRegisteredProtocols(): void
    {
        $filesystem = new Filesystem(new LocalFilesystemAdapter(sys_get_temp_dir()));
        FlysystemStreamWrapper::register('test1', $filesystem);
        FlysystemStreamWrapper::register('test2', $filesystem);

        $this->assertSame(['test1', 'test2'], FlysystemStreamWrapper::getRegisteredProtocols());
    }

    public function testUnregisterAll(): void
    {
        $filesystem = new Filesystem(new LocalFilesystemAdapter(sys_get_temp_dir()));
        FlysystemStreamWrapper::register('test1', $filesystem);
        FlysystemStreamWrapper::register('test2', $filesystem);

        $this->assertContains('test1', stream_get_wrappers());
        $this->assertContains('test2', stream_get_wrappers());

        FlysystemStreamWrapper::unregisterAll();

        $this->assertNotContains('test1', stream_get_wrappers());
        $this->assertNotContains('test2', stream_get_wrappers());
    }
}
