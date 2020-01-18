# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- `postcode:clear-cache` command, to remove cached postcodes on a tagged cache
- `Roelofr\PostcodeApi\Contracts\CacheServiceContract` for allowing you to skip
  writing cache-clearing logic if you don't need to.
- `Roelofr\PostcodeApi\Exceptions\CacheClearException` in case cache clearing
  goes south.

## [0.2.0] - 2020-01-18

### Changed
- Renamed Contract from `Roelofr\PostcodeApi\Contracts\PostcodeApiContract` →
  `Roelofr\PostcodeApi\Contracts\ServiceContract`

## [0.1.0] - 2020-01-18

### Added
First commit.

[Unreleased]: https://github.com/roelofr/postcode-api/compare/v0.2.0...HEAD
[0.2.0]: https://github.com/roelofr/postcode-api/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/roelofr/postcode-api/releases/tag/v0.1.0
