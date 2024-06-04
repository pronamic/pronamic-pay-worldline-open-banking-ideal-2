# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.2.0] - 2024-06-04

### Added

- Added support for iDEAL issuers to help smooth migration to iDEAL 2.0. ([#7](https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/issues/7))

### Composer

- Added `pronamic/ideal-issuers` `^1.0`.
- Changed `php` from `>=8.0` to `>=8.1`.
- Changed `automattic/jetpack-autoloader` from `v3.0.6` to `v3.0.8`.
	Release notes: https://github.com/Automattic/jetpack-autoloader/releases/tag/v3.0.8
- Changed `pronamic/pronamic-wp-updater` from `v1.0.1` to `v1.0.2`.
	Release notes: https://github.com/pronamic/pronamic-wp-updater/releases/tag/v1.0.2
- Changed `pronamic/wp-http` from `v1.2.2` to `v1.2.3`.
	Release notes: https://github.com/pronamic/wp-http/releases/tag/v1.2.3
- Changed `wp-pay/core` from `v4.16.0` to `v4.18.0`.
	Release notes: https://github.com/pronamic/wp-pay-core/releases/tag/v4.18.0

Full set of changes: [`1.1.0...1.2.0`][1.2.0]

[1.2.0]: https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/compare/v1.1.0...v1.2.0

## [1.1.0] - 2024-04-23

### Changed

- Use ID from ASPSP/iDEAL Hub as payment transaction ID. ([1d2e27d](https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/commit/1d2e27d4a77e5382d4107f1fffed5c0f7b5b5596))

### Composer

- Changed `automattic/jetpack-autoloader` from `v3.0.4` to `v3.0.6`.
	Release notes: https://github.com/Automattic/jetpack-autoloader/releases/tag/v3.0.6

Full set of changes: [`1.0.0...1.1.0`][1.1.0]

[1.1.0]: https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/compare/v1.0.0...v1.1.0

## [1.0.0] - 2024-03-26

- First relase.

[1.0.0]: https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/releases/tag/v1.0.0
