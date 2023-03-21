<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\StreamCommand;

use Amp\Parallel\Sync\SerializationException;
use Amp\Parallel\Sync\SharedMemoryException;
use Amp\Parallel\Sync\SharedMemoryParcel;
use Amp\Parallel\Worker\TaskFailureException;
use function Amp\ParallelFunctions\parallel;
use Amp\Promise;
use function Amp\Promise\all;
use function Amp\Promise\wait;
use Amp\Sync\SyncException;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\StreamLockCommand;
use M2MTech\FlysystemStreamWrapper\Tests\Assert;
use Throwable;

class StreamLockTest extends AbstractStreamCommandTest
{
    use Assert;

    /** @small */
    public function test(): void
    {
        $current = $this->getCurrent();

        $this->assertTrue(StreamLockCommand::run($current, LOCK_EX));
        $this->assertTrue(StreamLockCommand::run($current, LOCK_EX));

        $other = $this->getCurrent();
        $this->assertFalse(StreamLockCommand::run($other, LOCK_EX | LOCK_NB));

        $this->assertTrue(StreamLockCommand::run($current, LOCK_UN));
        $this->assertTrue(StreamLockCommand::run($other, LOCK_EX));

        $this->assertFalse(StreamLockCommand::run($current, LOCK_EX | LOCK_NB));
    }

    /** @medium */
    public function testParallelProcesses(): void
    {
        if (version_compare(PHP_VERSION, '8.1.0') >= 0) {
            $this->markTestSkipped('skipped parallel testing of locking for php8.1 till opis/closure is available in version 4.x');
        }

        try {
            $parcel = SharedMemoryParcel::create($id = uniqid('test', true), false);
        } catch (SharedMemoryException|SyncException $e) {
            $this->fail('cannot initiate shared memory');
        }

        try {
            $main = parallel(function () use ($id) {
                $current = $this->getCurrent();
                $this->assertTrue(
                    StreamLockCommand::run($current, LOCK_EX | LOCK_NB),
                    'Main process could not get lock'
                );

                // tell other process that the file is locked
                $parcel = SharedMemoryParcel::use($id);
                yield $parcel->synchronized(function () {
                    return true;
                });

                // wait till other process is waiting vor lock
                while (yield $parcel->unwrap()) {
                    usleep(10000);
                }

                // lock is released by dying process
                return 1;
            });

            $other = parallel(function () use ($id) {
                $parcel = SharedMemoryParcel::use($id);
                while (!yield $parcel->unwrap()) {
                    usleep(10000);
                }

                $other = $this->getCurrent();
                $this->assertFalse(
                    StreamLockCommand::run($other, LOCK_EX | LOCK_NB),
                    'Other process got lock when it should be owned by main process'
                );

                yield $parcel->synchronized(function () {
                    return false;
                });

                $this->assertTrue(
                    StreamLockCommand::run($other, LOCK_EX),
                    'Other process could not get lock'
                );

                return 2;
            });
        } catch (SerializationException $e) {
            $this->fail('this should not happen at runtime');
        }

        $result = [];
        try {
            /** @var array<Promise<int>> $promises */
            $promises = [$main(), $other()];
            $result = wait(all($promises));

            $parcel->unwrap();
        } catch (TaskFailureException $e) {
            $this->fail($e->getOriginalMessage());
        } catch (Throwable $e) {
            $this->fail('timeout reached');
        }

        $this->assertSame([1, 2], $result);

        if (version_compare(PHP_VERSION, '8.0.0') >= 0) {
            $this->expectErrorWithMessage('is deprecated', E_DEPRECATED);
        }
    }
}
