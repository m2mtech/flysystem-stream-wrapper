<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use ArrayIterator;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\DirReaddirCommand;
use PHPUnit\Framework\TestCase;

class DirReaddirTest extends TestCase
{
    public function test(): void
    {
        $current = new FileData();
        $current->dirListing = new ArrayIterator([
            new FileAttributes('one'),
            new FileAttributes('two'),
            new DirectoryAttributes('dir'),
            new FileAttributes('three'),
        ]);

        $this->assertSame('one', DirReaddirCommand::run($current));
        $this->assertSame('two', DirReaddirCommand::run($current));
        $this->assertSame('dir', DirReaddirCommand::run($current));
        $this->assertSame('three', DirReaddirCommand::run($current));
        $this->assertFalse(DirReaddirCommand::run($current));
    }
}
