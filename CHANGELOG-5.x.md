# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Fixed
- Fixed installing the Composer dependencies via the CI pipeline

## [5.9.1] - 2020-12-29
### Fixed
- [Files] Fixed downloading local files

## [5.9.0] - 2020-12-28
### Added
- [Core] Extended the ``ControllerActionRequestEvent`` with the ability to set a response
- [Core] Implemented the ``TerminableInterface`` to allow running some longer processes without affecting the client's response times
- [Core] Added the new interface ``SortingAwareInterface`` and trait `SortingAwareTrait` to extend the service models with the capability to change the order of DB results
- [Core] The ``AbstractNestedSetModel`` classes implement the `SortingAwareInterface` by default now
- [System] Allow to override the text-color of the "mandatory form-field star" using a SCSS variable
- [System] Allow to override the margins of the pagination using a SCSS variable

### Changed
- [Core] Increase the minimum supported PHP version to 7.2.5
- [Core] Reworked the bootstrapping process, which allowed us to greatly simplify the front controllers
- [Installer] Reworked the installer's requirements check, so that it automatically fetches the minimum required PHP version and to be installed PHP extensions from the ``composer.json`` files
- [SEO] Move the regeneration of the XML-sitemaps to be handled by the ``TerminateEvent``
- [SEO] Combined 2 event listeners into a single one (which doesn't gets called that often) so slightly improve the performance
- [System] Reworked the cache clear controller action page
- [System] Removed the ``<!-- JAVASCRIPT -->`` placeholder from the content-only layout file

### Deprecations
- [Core] Deprecated the method ``ACP3\Core\Helpers\Alerts::errorBoxContent()``. Use ``ACP3\Core\Helpers\Alerts::errorBox()`` instead.

### Fixed
- [Installer] Fixed the wrongly reported minimum PHP version of PHP 7.1.0 (now it is 7.2.5)
- [System] Fixed possibly wrong colors of the ``list-group-item`` overrides

## [5.8.0] - 2020-12-26
### Changed
- reworked the `ACP3\Core\Helpers\Alerts::confirmBox` and `ACP3\Core\Helpers\Alerts::confirmBoxPost` so that they return the already rendered template
  - This allowed us to remove all the `*.delete.tpl`-templates
- reworked the SchemaUpdater to be a little faster
- optimized the bootstrapping process to that a sub request doesn't trigger a complete rebuild of the DI container anymore
- optimized the inclusion of the reCaptcha assets, so that they are only loaded when really needed

### Fixed
- The update check event listener was called way to often. This has been fixed now

## [5.7.0] - 2020-12-22
### Added
- added the `acp3/module-menus` as a hard dependency to the `acp3/theme-default`

### Changed
- slightly reworked and improved the layout system to fix some long-standing bugs
- updated the NPM dependencies (especially the jQuery DataTables), to fix a possible security issue
- dropped the RefreshListener from the HttpCache to fix errors with the Firefox browser
- exclude non installable modules from the DB migrations
- throw an error if a requested assets can't be resolved
- removed all the leftover mentions of the articles module from the menus module
- moved all DB queries from the various nested set operations into its repository class

### Fixed
- Fixed saving ACL rules
- Fixed the RSS/Atom feeds when there are no items to display
- Fixed an incorrectly reported status when saving entities which are using the infrastructure of the `AbstractNestedSetModel`

## [5.6.0] - 2020-12-15
### Changed
- renamed the `LibraryDto` to `LibraryEntity`. This also removes the possibility to enable a frontend library by default --> you have to call "enableLibraries" explicitly

## [5.5.1] - 2020-12-15
### Fixed
- Fixed the error `Uncaught ReferenceError: jQuery is not defined` when the reCaptcha is active

## [5.5.0] - 2020-12-13
### Added
- Made it possible to load some CSS files asynchronously
- Continued to add SCSS variables to various modules

### Changed
- `defer` loading the main javascript assets
- The `ajax-form` library isn't enabled by default anymore
- Refactored the poll creation form to use the ajax-form layout

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

[Unreleased]: https://gitlab.com/ACP3/cms/compare/v5.9.1...5.x
[5.9.1]: https://gitlab.com/ACP3/cms/compare/v5.9.0...v5.9.1
[5.9.0]: https://gitlab.com/ACP3/cms/compare/v5.8.0...v5.9.0
[5.8.0]: https://gitlab.com/ACP3/cms/compare/v5.7.0...v5.8.0
[5.7.0]: https://gitlab.com/ACP3/cms/compare/v5.6.0...v5.7.0
[5.6.0]: https://gitlab.com/ACP3/cms/compare/v5.5.1...v5.6.0
[5.5.1]: https://gitlab.com/ACP3/cms/compare/v5.5.0...v5.5.1
[5.5.0]: https://gitlab.com/ACP3/cms/compare/v5.4.0...v5.5.0
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
