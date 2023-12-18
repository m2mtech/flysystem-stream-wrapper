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

The stream wrapper implements [`symfony/lock`](https://symfony.com/doc/current/components/lock.html) due to Flysystem V2 not supporting locking. By default, file locking using `/tmp` is used, but you can adjust this through configuration:

```php
FlysystemStreamWrapper::register('fly', $filesystem, [
    FlysystemStreamWrapper::LOCK_STORE => 'flock:///tmp',
    FlysystemStreamWrapper::LOCK_TTL => 300,
]);
```

### Handling Visibility Issues

Some adaptors might throw exceptions when dealing with visibility. If you encounter such issues, configure the stream wrapper to bypass them:

```php
FlysystemStreamWrapper::register('fly', $filesystem, [
    FlysystemStreamWrapper::IGNORE_VISIBILITY_ERRORS => true,
]);
```

### Addressing Directory Issues (`file_exists` / `is_dir`)

Some adaptors might not return dates for the last modified attribute for directories. In such cases, you can enable emulation to achieve the desired behavior:

```php
FlysystemStreamWrapper::register('fly', $filesystem, [
    FlysystemStreamWrapper::EMULATE_DIRECTORY_LAST_MODIFIED => true,
]);
```

### Dealing with `is_readable` / `is_writable`

Some filesystem functions depend on the `uid` and `gid` of the user executing PHP. Since a reliable cross-platform method to derive these values isn't available, the wrapper attempts to estimate them. If this fails, set them manually:

```php
FlysystemStreamWrapper::register('fly', $filesystem, [
    FlysystemStreamWrapper::UID => 1000,
    FlysystemStreamWrapper::GID => 1000,
]);
```

Alternatively, access the parameters for [`PortableVisibilityConverter`](https://flysystem.thephpleague.com/docs/usage/unix-visibility/) directly:

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

This package was developed using PHP 7.4 and has been tested for compatibility with PHP versions 7.2 through 8.3.

To test:

- With PHP installed:
```bash
composer test
```

- Inside a Docker environment for PHP 7.4:
```bash
docker compose run php74 composer test
```

**Note**: PHPUnit v10, used from PHP 8.1 onwards, requires a different config file:
```bash
docker compose run php81 composer test10
```

## Changelog

For information on recent changes, refer to the [CHANGELOG](CHANGELOG.md).

## Contributing

For contribution guidelines, see [CONTRIBUTING](.github/CONTRIBUTING.md).

## Security Vulnerabilities

If you discover any security vulnerabilities, please follow [our security policy](../../security/policy) for reporting.

## Credits

- This package was inspired by [twistor/flysystem-stream-wrapper](https://github.com/twistor/flysystem-stream-wrapper). Many thanks to [Chris Leppanen](https://github.com/twistor).
- [All Contributors](../../contributors)

## License

Licensed under the MIT License. See the [License File](LICENSE.md) for more details.
