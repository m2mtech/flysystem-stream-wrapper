# Flysystem Stream Wrapper

[![Author](https://img.shields.io/badge/author-@m2mtech-blue.svg?style=flat-square)](http://www.m2m.at)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

---

This package provides a stream wrapper for Flysystem V2 & V3.

## Flysystem V1

If you're looking for Flysystem 1.x support, check out the [twistor/flysystem-stream-wrapper](https://github.com/twistor/flysystem-stream-wrapper), on which this package is based on.

This project is a complete rewrite and has just the name and the functionality as stream wrapper in common with the V1 package. Please note that there is also a recent [pull request for V2](https://github.com/twistor/flysystem-stream-wrapper/pull/26) waiting though the last merge seems to be from November 2018. Thus, a new package seemed to be reasonable.

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

Some adaptors seem to throw an exception when visibility is used. To be able to use such adaptors, tell the stream wrapper to ignore them, e.g.:

```php
FlysystemStreamWrapper::register('fly', $filesystem, [
    FlysystemStreamWrapper::IGNORE_VISIBILITY_ERROS => true,
]);
```


## Testing

This package has been developed for php 7.4 with compatibility tested for php 7.2 to 8.1.

```bash
# with php installed
composer test

# or inside docker e.g. for php 7.4
docker-compose run php74 composer test
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
