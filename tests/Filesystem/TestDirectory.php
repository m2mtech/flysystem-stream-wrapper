<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Tests\Filesystem;

class TestDirectory extends AbstractTestObject
{
    public function createFile(bool $withContent = false): TestFile
    {
        $file = new TestFile($this);

        if (!$withContent) {
            return $file;
        }

        $fileWithContent = $this->faker->file('tests', $this->local);
        rename($fileWithContent, $file->local);

        return $file;
    }

    public function createDirectory(bool $create = false): TestDirectory
    {
        $directory = new TestDirectory($this);

        if ($create) {
            mkdir($directory->local);
        }

        return $directory;
    }

    public function removeLocalDirectory(?string $dir = null): void
    {
        if (null === $dir) {
            $dir = $this->local;
        }

        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        if (!is_array($items)) {
            rmdir($dir);

            return;
        }

        foreach ($items as $item) {
            if ('.' === $item || '..' === $item) {
                continue;
            }

            if (is_dir($dir.DIRECTORY_SEPARATOR.$item) && !is_link($dir.DIRECTORY_SEPARATOR.$item)) {
                $this->removeLocalDirectory($dir.DIRECTORY_SEPARATOR.$item);

                continue;
            }

            unlink($dir.DIRECTORY_SEPARATOR.$item);
        }
        rmdir($dir);
    }
}
