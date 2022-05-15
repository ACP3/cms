# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [6.0.2] - 2022-05-15

### Fixed

-   [Emoticons] Harden the EmoticonService against non-existing files

## [6.0.1] - 2022-05-15

### Fixed

-   [Installer] Fixed updating existing installation from version 5.x to 6.0.0

## [6.0.0] - 2022-05-14

### BC breaks

-   [All] Dropped support for PHP versions < 8.0
-   [All] Reworked the DB-Migrations. Implement the new `ACP3\Core\Migration\MigrationInterface`
-   [All] Removed all deprecated code
-   [Core] Renamed `ACP3\Core\Controller\Context\WidgetContext` to `ACP3\Core\Controller\Context\Context`
-   [System] Updated Bootstrap to version 5.1.3
-   [System] Updated Fontawesome to version 6
-   [System] Dropped flatpickr. The system uses native HTML5 datetime-local inputs now

### Added

-   [All] Added many type declarations and made use of new language features coming with PHP version 7.4 and 8.0
-   [Base] Added official support for PHP 8.1

### Changed

-   [All] Reworked most of the JavaScript assets to not be dependent on jQuery anymore
-   [Base] Bumped PHPstan level from 5 to 6
-   [Permissions] Removed the `privilege_id` concept

### Fixed

-   [All] Fixed many bugs

[unreleased]: https://gitlab.com/ACP3/cms/compare/v6.0.2...6.x
[6.0.2]: https://gitlab.com/ACP3/cms/compare/v6.0.1...v6.0.2
[6.0.1]: https://gitlab.com/ACP3/cms/compare/v6.0.0...v6.0.1
[6.0.0]: https://gitlab.com/ACP3/cms/compare/v5.21.0...v6.0.0
