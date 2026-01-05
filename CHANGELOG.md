# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.4.0] - 2026-01-05

### Commits

- Removed unused use statements ([36df90b](https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/commit/36df90bb3dc2c05dcad8b5a650ee6d6bcdaea126))
- Improve or add support for Composer first WordPress setups, closes #12 ([d1b4091](https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/commit/d1b4091a8a52d71d775b7821e168a2ac7cdc277a))

### Composer

- Changed `automattic/jetpack-autoloader` from `v5.0.7` to `v5.0.15`.
	Release notes: https://github.com/Automattic/jetpack-autoloader/releases/tag/v5.0.15

Full set of changes: [`1.3.2...1.4.0`][1.4.0]

[1.4.0]: https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/compare/v1.3.2...v1.4.0

## [1.3.2] - 2025-11-11

### Commits

- Added this point we shoud not use translation functions. ([5133e4b](https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/commit/5133e4b7191ba4d056de5450a5d440f191a1883a))

Full set of changes: [`1.3.1...1.3.2`][1.3.2]

[1.3.2]: https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/compare/v1.3.1...v1.3.2

## [1.3.1] - 2025-06-19

### Commits

- Also allow Jetpack autoloader 4 and 5. ([499b653](https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/commit/499b653044acde38d9fd3f5cacb03f712d908f8d))

### Composer

- Changed `automattic/jetpack-autoloader` from `v3.1.3` to `v5.0.7`.
	Release notes: https://github.com/Automattic/jetpack-autoloader/releases/tag/v5.0.7
- Changed `pronamic/pronamic-wp-updater` from `v1.0.2` to `v1.0.3`.
	Release notes: https://github.com/pronamic/pronamic-wp-updater/releases/tag/v1.0.3

Full set of changes: [`1.3.0...1.3.1`][1.3.1]

[1.3.1]: https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/compare/v1.3.0...v1.3.1

## [1.3.0] - 2025-06-19

### Removed

- Removed issuer field from iDEAL payment method. ([c656159](https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/commit/c6561590938c28871ae817b9e2a1cc3e8eab15b3))

### Composer

- Removed `pronamic/ideal-issuers` `^1.0`.
- Changed `automattic/jetpack-autoloader` from `v3.0.8` to `v3.1.3`.
	Release notes: https://github.com/Automattic/jetpack-autoloader/releases/tag/v3.1.3
- Changed `pronamic/wp-http` from `v1.2.3` to `v1.2.4`.
	Release notes: https://github.com/pronamic/wp-http/releases/tag/v1.2.4
- Changed `wp-pay/core` from `v4.18.0` to `v4.26.0`.
	Release notes: https://github.com/pronamic/wp-pay-core/releases/tag/v4.26.0

Full set of changes: [`1.2.1...1.3.0`][1.3.0]

[1.3.0]: https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/compare/v1.2.1...v1.3.0

## [1.2.1] - 2024-12-17

### Commits

- Created iDEAL_2.0_Getting_Started_Guide_-_version_1.5 - ABN AMRO.pdf ([d67de6a](https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/commit/d67de6a98e88b7ef8ff40fe41b98d996bf115513))
- Updated CHANGELOG.md ([0d96476](https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/commit/0d96476c44c74d9303efeb4b9f379e8402544dfc))

Full set of changes: [`1.2.0...1.2.1`][1.2.1]

[1.2.1]: https://github.com/pronamic/pronamic-pay-worldline-open-banking-ideal-2/compare/v1.2.0...v1.2.1

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
