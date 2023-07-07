# Flysystem Stream Wrapper

[![Author](https://img.shields.io/badge/author-@m2mtech-blue.svg?style=flat-square)](http://www.m2m.at)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

---

This package provides a stream wrapper for Flysystem V2 & V3.

## Flysystem V1

If you're looking for Flysystem 1.x support, check out the [twistor/flysystem-stream-wrapper](https://github.com/twistor/flysystem-stream-wrapper).

## Installation

```bash
composer require m2mtech/flysystem-stream-wrapper
```

## Usage

```php
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use M2MTech\FlysystemStreamWrapper\FlysystemStreamWrapper;

$filesystem = new Filesystem(new LocalFilesystemAdapter('/some/path'));
FlysystemStreamWrapper::register('fly', $filesystem);

file_put_contents('fly://filename.txt', $content);
mkdir('fly://happy_thoughts');

FlysystemStreamWrapper::unregister('fly');
```

Because locking is not supported by Flysystem V2, the stream wrapper implements [`symfony/lock`](https://symfony.com/doc/current/components/lock.html). As default, it uses file locking using `/tmp`, which you can adjust via the configuration:

```php
FlysystemStreamWrapper::register('fly', $filesystem, [
    FlysystemStreamWrapper::LOCK_STORE => 'flock:///tmp',
    FlysystemStreamWrapper::LOCK_TTL => 300,
]);
```

### Problems with Visibility

Some adaptors seem to throw an exception when visibility is used. To be able to use such adaptors, tell the stream wrapper to ignore them, e.g.:

```php
FlysystemStreamWrapper::register('fly', $filesystem, [
    FlysystemStreamWrapper::IGNORE_VISIBILITY_ERRORS => true,
]);
```

### Problems with Directories (`file_exists` / `is_dir`)

To provide fully transparent behaviour for directories enable the emulation of
last modified timestamp, and ignore visibility errors, e.g.:

```php
FlysystemStreamWrapper::register('fly', $filesystem, [
    FlysystemStreamWrapper::IGNORE_VISIBILITY_ERRORS => true,
    FlysystemStreamWrapper::EMULATE_DIRECTORY_LAST_MODIFIED => true,
]);
```

### Problems with `is_readable` / `is_writable`

A couple of filesystem functions use `uid` and `gid` of the user running php. Unfortunately there is no straight forward cross-plattform usable method available to derive those values. The wrapper tries to *guess* them. But depending on your system settings it might fail.
In such cases you can set them manually, e.g.:

```php
FlysystemStreamWrapper::register('fly', $filesystem, [
    FlysystemStreamWrapper::UID => 1000,
    FlysystemStreamWrapper::GID => 1000,
]);
```

or go haywire accessing the parameters for [`PortableVisibilityConverter`](https://flysystem.thephpleague.com/docs/usage/unix-visibility/) directly via:

```php
FlysystemStreamWrapper::register('fly', $filesystem, [
    FlysystemStreamWrapper::VISIBILITY_FILE_PUBLIC => 0644,
    FlysystemStreamWrapper::VISIBILITY_FILE_PRIVATE => 0600,
    FlysystemStreamWrapper::VISIBILITY_DIRECTORY_PUBLIC => 0755,
    FlysystemStreamWrapper::VISIBILITY_DIRECTORY_PRIVATE => 0700,
    FlysystemStreamWrapper::VISIBILITY_DEFAULT_FOR_DIRECTORIES => Visibility::PRIVATE,
]);
```

## Testing

This package has been developed for php 7.4 with compatibility tested for php 7.2 to 8.2.

```bash
# with php installed
composer test

# or inside docker e.g. for php 7.4
docker-compose run php74 composer test

# note that phpunit v10 used stating php 8.1 will require to use a different config file:
docker-compose run php81 vendor/bin/phpunit -c phpunit.10.xml
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- This package is based on [twistor/flysystem-stream-wrapper]. Kudos and thanks to [Chris Leppanen](https://github.com/twistor).
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
