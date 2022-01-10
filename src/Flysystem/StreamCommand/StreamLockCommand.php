<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand;

use M2MTech\FlysystemStreamWrapper\Flysystem\FileData;
use M2MTech\FlysystemStreamWrapper\FlysystemStreamWrapper;
use Symfony\Component\Lock\Key;
use Symfony\Component\Lock\Lock;
use Symfony\Component\Lock\Store\StoreFactory;

final class StreamLockCommand
{
    public static function run(FileData $current, int $operation): bool
    {
        if (null === $current->lockKey) {
            $current->lockKey = new Key($current->path);
        }

        $store = StoreFactory::createStore((string) $current->config[FlysystemStreamWrapper::LOCK_STORE]);
        $lock = new Lock(
            $current->lockKey,
            $store,
            (float) $current->config[FlysystemStreamWrapper::LOCK_TTL],
            false
        );

        switch ($operation) {
            case LOCK_SH:
                return $lock->acquireRead(true);

            case LOCK_EX:
                return $lock->acquire(true);

            case LOCK_UN:
                $lock->release();

                return true;

            case LOCK_SH | LOCK_NB:
                return $lock->acquireRead();

            case LOCK_EX | LOCK_NB:
                return $lock->acquire();
        }

        return false;
    }
}
