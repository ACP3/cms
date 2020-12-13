# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Added
- Continued to add SCSS variables to various modules

### Changed
- `defer` loading the main javascript assets

### Fixed
- Fixed that the social sharing module javascript assets where not being placed at the end of the `<body>`-tag

## [5.4.0] - 2020-12-12
### Added
- Extended the SCSS files of the various modules with SCSS variables to make it easier to customize the ACP3 default appearance

### Fixed
- Fixed the 404 error when trying to enter the social sharing module's settings page
- Fixed the visuals of the rating stars of the social sharing module when ACP3's default theme is active

## [5.3.2] - 2020-12-12
### Fixed
- Fixed a bug within the `AbstractModel`'s change detection

## [5.3.1] - 2020-12-12
### Fixed
- Fixed the gallery list when there galleries without pictures

## [5.3.0] - 2020-12-05
### Changed
- Updated the composer dependencies
- Updated the NPM dependencies
- Updated the PHPUnit tests according to changes introduced in PHPUnit 8.x
- Replaced jQuery(document).ready() calls self-calling closures

### Fixed
- Fixed the filemanager
- fixed the missing fontawesome icons within the installer/update wizard

## [5.2.0] - 2020-11-29
### Changed
- rework the AbstractModifier so that it allows more than one parameter

## [5.1.2] - 2020-11-27
### Fixed
- fixed the missing sort icons within the data grids with the default theme
- fixed some wrong icons

## [5.1.1] - 2020-11-22
### Fixed
- version bump only

## [5.1.0] - 2020-11-22
### Changed
- Extracted the cookie consent into its own module and reworked it
- Replace the glyphicons with the font-awesome icons

## [5.0.1] - 2020-08-05
### Changed
- optimized the module installation (fixed some n+1 SQL queries)
- restricted the POST-only controllers actions to "normal" forms

### Fixed
- fixed erroneous entries within the settings tables after a fresh installation

## [5.0.0] - 2020-07-19
### Breaking
- removed all deprecated code

[Unreleased]: https://gitlab.com/ACP3/cms/compare/v5.4.0...5.x
[5.4.0]: https://gitlab.com/ACP3/cms/compare/v5.3.2...v5.4.0
[5.3.2]: https://gitlab.com/ACP3/cms/compare/v5.3.1...v5.3.2
[5.3.1]: https://gitlab.com/ACP3/cms/compare/v5.3.0...v5.3.1
[5.3.0]: https://gitlab.com/ACP3/cms/compare/v5.2.0...v5.3.0
[5.2.0]: https://gitlab.com/ACP3/cms/compare/v5.1.2...v5.2.0
[5.1.2]: https://gitlab.com/ACP3/cms/compare/v5.1.2...5.1.1
[5.1.1]: https://gitlab.com/ACP3/cms/compare/v5.1.1...5.1.0
[5.1.0]: https://gitlab.com/ACP3/cms/compare/v5.1.0...v5.0.1
[5.0.1]: https://gitlab.com/ACP3/cms/compare/v5.0.0...v5.0.1
[5.0.0]: https://gitlab.com/ACP3/cms/compare/v4.x...v5.0.0
