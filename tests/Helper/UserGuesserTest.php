<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2022-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\Helper;

use M2MTech\FlysystemStreamWrapper\Flysystem\Helper\UserGuesser;
use PHPUnit\Framework\TestCase;

class UserGuesserTest extends TestCase
{
    public function test(): void
    {
        if (!function_exists('posix_getuid') || !function_exists('posix_getgid')) {
            $this->assertTrue(true);

            return;
        }

        $this->assertEquals(posix_getuid(), UserGuesser::getUID());
        $this->assertEquals(posix_getgid(), UserGuesser::getGID());
    }
}
