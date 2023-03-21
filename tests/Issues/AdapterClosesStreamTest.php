<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\Issues;

use M2MTech\FlysystemStreamWrapper\Flysystem\StreamWrapper;
use M2MTech\FlysystemStreamWrapper\Tests\Assert;
use M2MTech\FlysystemStreamWrapper\Tests\StreamCommand\AbstractStreamCommandTest;

class AdapterClosesStreamTest extends AbstractStreamCommandTest
{
    use Assert;

    public function testStreamClose(): void
    {
        $current = $this->getCurrent();
        $current->handle = fopen('php://temp', 'wb');
        $wrapper = new StreamWrapper($current);

        if (!is_resource($current->handle)) {
            $this->fail();
        }

        // adapter closes stream itself
        fclose($current->handle);
        $this->assertIsClosedResource($current->handle);

        $wrapper->stream_close();
        $this->assertIsClosedResource($current->handle);
    }
}
