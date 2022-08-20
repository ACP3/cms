# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [6.5.1] - 2022-08-20

### Fixed

-   [Menus] Fixed the bug that the page-URI was missing after saving a menu item
-   [Menus] Fixed the bug that the parent-menu-item-form-field was mandatory

## [6.5.0] - 2022-08-20

### Added

-   [Files] made it possible to add a subtitle to downloads
-   [Files] enabled the full WYSIWYG editor when creating/editing a download
-   [Gallery] made it possible to add a subtitle to galleries
-   [Gallery] enabled the full WYSIWYG editor when creating/editing a gallery
-   [News] made it possible to add a subtitle to news

### Changed

-   [ALL] replaced some magic numbers with the newly introduced enums `YesNoEnum`, `LinkTargetEnum` and `NewsletterSendingStatusEnum`
-   [Files] "un-boxed" the files details page
-   [Files] increased the maximum length of the title to 255 characters
-   [News] "un-boxed" the news details page
-   [News] increased the maximum length of the title to 255 characters

### Fixed

-   [Core] Removed the `strict_types` declaration from the `ControllerActionDispatcher` to remedy some hard to fix possible `TypeError`s

## [6.4.0] - 2022-08-16

### Added

-   [System] [Gallery] Added photoswipe as a replacement for fancybox

### Changed

-   [Gallery] Removed the `overlay` setting from the gallery module

### Fixed

-   [User] Fixed the user login form

### Deprecated

-   [System] Deprecated fancybox, use photoswipe instead.

## [6.3.1] - 2022-08-15

### Fixed

-   [System] Add a missing space before the `$formGroupSelector`

## [6.3.0] - 2022-08-14

### Added

-   [System] Added some new form-group partials template and extended the existing ones with new functionality, to make them even more reusable

### Changed

-   [System] Display readonly and disabled form inputs as plaintext
-   [All] Changed the default breakpoints from "sm" (576px) to "md" (768px). The default form-breakpoint can now be configured with the variable `$formBreakpoint`.
-   [Core] Changed the repo-URI of the forked RichFileManager-NPM-package, so that it doesn't require Git to be present anymore
-   [Base] Remove the PHP-docker-container again, when it's finished linting and testing as part of the pre-commit-hook

### Fixed

-   [System] Fixed the missing alert, when page cache purging is set to "manual" and the page cache is not valid.
-   [All] Fixed various minor visual glitches

## [6.2.1] - 2022-08-08

### Fixed

-   [SEO] Fixed saving result sets, when the SEO module integration is active

## [6.2.0] - 2022-08-07

### Added

-   [Core] [SEO] Added the possibility to specify canonical URIs via the UI
-   [Core] Added the `MetaRobotsEnum`
-   [Menus] Added the `PageTypeEnum`
-   [Permissions] Added the `ProtectedRolesEnum`
-   [System] Added the `SiteSubtitleModeEnum`
-   [Users] Added the `GenderEnum`

### Changed

-   [Articles] [Files] [News] Moved the fulltext index creating into the corresponding ``*search`-modules
-   [Core] Removed the deprecated `StreamedResponseListener` as it is not needed anymore

### Fixed

-   [Articlessearch] [Filessearch] [Newssearch] Fixed missing fulltext indexes when only searching for `title` or `content`
-   [SEO] Fixed the missing `structured_data`-column after installing the ACP3
-   [System] Fixed the missing "extensions" and "maintenance" menu items in the user menu

## [6.1.5] - 2022-08-04

-   [Core] Fixed the `Concat*Renderer`-Strategies

## [6.1.4] - 2022-07-31

### Fixed

-   [Core] Fixed a minor bug in the breadcrumb generation

## [6.1.3] - 2022-07-31

### Fixed

-   [Core] Reverted an overly strict type annotation within the `Validator::is()`-method

## [6.1.2] - 2022-07-31

### Fixed

-   [Core] Fixed a bug within the `PictureValidationRule`

## [6.1.1] - 2022-07-31

### Changed

-   [Core] Improved a type annotation in the table-of-contents generator

## [6.1.0] - 2022-07-30

### Changed

-   [Core] Updated symfony components to version 6.1
-   [Core] Deprecated the `BaseEnum`-class
-   [Core] Converted the `PermissionEnum` into a native PHP-enum
-   [Core] Converted the `PrivilegeEnum` into a native PHP-enum
-   [Core] Converted the `AreaEnum` into a native PHP-enum
-   [Core] Converted the `PluginTypeEnum` into a native PHP-enum
-   [Core] Converted the `ApplicationModeEnum` into a native PHP-enum
-   [SEO] Converted the `IndexPaginatedContentEnum` into a native PHP-enum
-   [System] Updated Bootstrap to version 5.2.0

### Breaking

-   [Core] Dropped support for PHP 8.0.x. PHP 8.1.x. is required now.

## [6.0.4] - 2022-05-23

### Changed

-   [System] Update the NPM dependencies

### Fixed

-   [Core] Fix a possible PHP error, when providing an empty path to the `ControllerActionExists::controllerActionExists`-method

## [6.0.3] - 2022-05-16

### Fixed

-   [Core] Harden the `ExternalLinkValidationRule` against non-existing form values
-   [Categories] Harden the `CategoryExistsValidationRule` against non-existing form values

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

[unreleased]: https://gitlab.com/ACP3/cms/compare/v6.5.1...6.x
[6.5.1]: https://gitlab.com/ACP3/cms/compare/v6.5.0...v6.5.1
[6.5.0]: https://gitlab.com/ACP3/cms/compare/v6.4.0...v6.5.0
[6.4.0]: https://gitlab.com/ACP3/cms/compare/v6.3.1...v6.4.0
[6.3.1]: https://gitlab.com/ACP3/cms/compare/v6.3.0...v6.3.1
[6.3.0]: https://gitlab.com/ACP3/cms/compare/v6.2.1...v6.3.0
[6.2.1]: https://gitlab.com/ACP3/cms/compare/v6.2.0...v6.2.1
[6.2.0]: https://gitlab.com/ACP3/cms/compare/v6.1.5...v6.2.0
[6.1.5]: https://gitlab.com/ACP3/cms/compare/v6.1.4...v6.1.5
[6.1.4]: https://gitlab.com/ACP3/cms/compare/v6.1.3...v6.1.4
[6.1.3]: https://gitlab.com/ACP3/cms/compare/v6.1.2...v6.1.3
[6.1.2]: https://gitlab.com/ACP3/cms/compare/v6.1.1...v6.1.2
[6.1.1]: https://gitlab.com/ACP3/cms/compare/v6.1.0...v6.1.1
[6.1.0]: https://gitlab.com/ACP3/cms/compare/v6.0.4...v6.1.0
[6.0.4]: https://gitlab.com/ACP3/cms/compare/v6.0.3...v6.0.4
[6.0.3]: https://gitlab.com/ACP3/cms/compare/v6.0.2...v6.0.3
[6.0.2]: https://gitlab.com/ACP3/cms/compare/v6.0.1...v6.0.2
[6.0.1]: https://gitlab.com/ACP3/cms/compare/v6.0.0...v6.0.1
[6.0.0]: https://gitlab.com/ACP3/cms/compare/v5.21.0...v6.0.0
