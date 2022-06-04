<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\FileCommand;

abstract class AbstractStatTest extends AbstractFileCommandTest
{
    use DataProvider;

    private const STATS_ZERO = [0, 'dev', 1, 'ino', 3, 'nlink', 6, 'rdev'];
    private const STATS_MODE = [2, 'mode'];
    private const STATS_SIZE = [7, 'size'];
    private const STATS_TIME = [8, 'atime', 9, 'mtime', 10, 'ctime'];
    private const STATS_MINUS_ONE = [11, 'blksize', 12, 'blocks'];

    /** @return array<int|string, int> */
    protected function expectedStats(string $file, int $permission): array
    {
        static $stats = [];
        if (!$stats) {
            foreach (self::STATS_ZERO as $key) {
                $stats[$key] = 0;
            }
            foreach (self::STATS_MINUS_ONE as $key) {
                $stats[$key] = -1;
            }
        }

        $mode = $permission + (is_file($file) ? 0100000 : 040000);
        foreach (self::STATS_MODE as $key) {
            $stats[$key] = $mode;
        }

        $size = (int) filesize($file);
        foreach (self::STATS_SIZE as $key) {
            $stats[$key] = $size;
        }

        $time = (int) filemtime($file);
        foreach (self::STATS_TIME as $key) {
            $stats[$key] = $time;
        }

        $stats['uid'] = $stats[4] = getmyuid();
        $stats['gid'] = $stats[5] = getmygid();

        return $stats;
    }
}
