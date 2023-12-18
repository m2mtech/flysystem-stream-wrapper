# Changelog

## [1.4.0] - 2023-12-18
- compatibility with symfony 7.x & php 8.3
- allow ecs 12.x
- added `EMULATE_DIRECTORY_LAST_MODIFIED`, thanks to @das-peter

## [1.3.3] - 2023-03-27
- fixed tests for phpunit v10

## [1.3.2] - 2023-03-21
- allow `bool` in `FlysystemStreamWrapper::register` configuration
- fixed phpunit deprecations  
- updated (c) dates

## [1.3.1] - 2022-08-12
- intercept TypeError when adapter returns null for visibility

## [1.3.0] - 2022-07-21
- adjusted uid & gid retrieval 
- added manual uid & gid setup
- allow to access parameters of [`PortableVisibilityConverter`](https://flysystem.thephpleague.com/docs/usage/unix-visibility/)
- fixed typo in const `IGNORE_VISIBILITY_ERRORS`

## [1.2.1] - 2022-06-10
- added intl for dev to fix ecs dependency
- fix for resources closed by flysystem prematurely

## [1.2.0] - 2022-06-04
- set current user uid & gid in stats

## [1.1.0] - 2022-05-05
- allow for adaptors closing the file handle themselves
- fixed broken directory detection caused by changed MimeType detection of [Flysystem 3.0.16](https://github.com/thephpleague/flysystem/compare/3.0.15...3.0.16)
- allow to ignore visibility errors

## [1.0.3] - 2022-01-21
- allow flysystem v3

## [1.0.2] - 2022-01-10
- replace section in [composer.json](composer.json)

## [1.0.1] - 2021-12-29
- compatibility with symfony 6.x
- skipped unit tests of locking for php8.1 till opis/closure is available in version 4.x (only used for testing)

## [1.0.0] - 2021-11-01
- Initial release 

<!---
## [Unreleased]
### Changed
- ...
--->

[Unreleased]: https://github.com/m2mtech/flysystem-stream-wrapper/compare/v1.3.3...HEAD
[1.4.0]: https://github.com/m2mtech/flysystem-stream-wrapper/compare/v1.3.3...v1.4.0
[1.3.3]: https://github.com/m2mtech/flysystem-stream-wrapper/compare/v1.3.2...v1.3.3
[1.3.2]: https://github.com/m2mtech/flysystem-stream-wrapper/compare/v1.3.1...v1.3.2
[1.3.1]: https://github.com/m2mtech/flysystem-stream-wrapper/compare/v1.3.0...v1.3.1
[1.3.0]: https://github.com/m2mtech/flysystem-stream-wrapper/compare/v1.2.1...v1.3.0
[1.2.1]: https://github.com/m2mtech/flysystem-stream-wrapper/compare/v1.2.0...v1.2.1
[1.2.0]: https://github.com/m2mtech/flysystem-stream-wrapper/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/m2mtech/flysystem-stream-wrapper/compare/v1.0.3...v1.1.0
[1.0.3]: https://github.com/m2mtech/flysystem-stream-wrapper/compare/v1.0.2...v1.0.3
[1.0.2]: https://github.com/m2mtech/flysystem-stream-wrapper/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/m2mtech/flysystem-stream-wrapper/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/m2mtech/flysystem-stream-wrapper/releases/tag/v1.0.0
<!---
[Unreleased]: https://github.com/m2mtech/flysystem-stream-wrapper/compare/v1.3.2...HEAD
--->
