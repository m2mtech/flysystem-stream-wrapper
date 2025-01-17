<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2023 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace M2MTech\FlysystemStreamWrapper\Flysystem;

use League\Flysystem\FilesystemException;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\ExceptionHandler;
use M2MTech\FlysystemStreamWrapper\Flysystem\StreamCommand\StreamWriteCommand;

/**
 * @method url_stat(string $path, int $int)
 */
final class StreamWrapper
{
    use ExceptionHandler;

    /** @var FileData */
    private $current;

    public function __construct(?FileData $current = null)
    {
        $this->current = $current ?? new FileData();
    }

    /**
     * @param array<int|string> $args
     *
     * @return array<int|string,int|string>|string|bool
     */
    public function __call(string $method, array $args)
    {
        $class = __NAMESPACE__.'\\StreamCommand\\'.str_replace('_', '', ucwords($method, '_')).'Command';
        if (class_exists($class)) {
            return $class::run($this->current, ...$args);
        }

        return false;
    }

    /** @var resource */
    public $context;

    public function dir_closedir(): bool
    {
        unset($this->current->dirListing);

        return true;
    }

    public function stream_close(): void
    {
        if (!is_resource($this->current->handle)) {
            return;
        }

        if ($this->current->workOnLocalCopy) {
            fflush($this->current->handle);
            rewind($this->current->handle);

            try {
                $this->current->filesystem->writeStream($this->current->file, $this->current->handle);
            } catch (FilesystemException $e) {
                trigger_error(
                    'stream_close('.$this->current->path.') Unable to sync file : '.self::collectErrorMessage($e),
                    E_USER_WARNING
                );
            }
        }

        fclose($this->current->handle);
    }

    public function stream_flush(): bool
    {
        if (!is_resource($this->current->handle)) {
            trigger_error(
                'stream_flush(): Supplied resource is not a valid stream resource',
                E_USER_WARNING
            );

            return false;
        }

        $success = fflush($this->current->handle);

        if ($this->current->workOnLocalCopy) {
            fflush($this->current->handle);
            $currentPosition = ftell($this->current->handle);
            rewind($this->current->handle);

            try {
                $this->current->filesystem->writeStream($this->current->file, $this->current->handle);
            } catch (FilesystemException $e) {
                trigger_error(
                    'stream_flush('.$this->current->path.') Unable to sync file : '.self::collectErrorMessage($e),
                    E_USER_WARNING
                );
                $success = false;
            }

            if (false !== $currentPosition) {
                if (is_resource($this->current->handle)) {
                    fseek($this->current->handle, $currentPosition);
                }
            }
        }

        $this->current->bytesWritten = 0;

        return $success;
    }

    /** @return array<int|string,int|string>|false */
    public function stream_stat()
    {
        return $this->url_stat($this->current->path, 0);
    }

    public function stream_write(string $data): int
    {
        $size = StreamWriteCommand::run($this->current, $data);

        if ($this->current->writeBufferSize && $this->current->bytesWritten >= $this->current->writeBufferSize) {
            $this->stream_flush();
        }

        return $size;
    }
}
