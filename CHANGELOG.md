# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [4.3.6] - 2016-11-03
### Added
- automatically create the uploads/assets directory if it is missing
- the `\ACP3\Core\Cache\Purge::purgeCurrentDirectory()` method now handles symbolic links gracefully too

### Fixed
- [#46](https://github.com/ACP3/cms/issues/46) fixed the disappearing file size unit when editing a download

## [4.3.5] - 2016-11-02
### Changed
- improved the performance of the `\ACP3\Core\Cache\Purge::doPurge()` method 

### Fixed
- fixed a character encoding problem when trying to use formatted HTML/XML code with the WYSIWYG-Editor inputs
- minor fixes for the default frontend templates of the gallery and files modules

## [4.3.4] - 2016-11-02
### Changed
- refined the default ACP3 design

### Fixed
- fixed the missing gallery title when saving a gallery

## [4.3.3] - 2016-11-01
### Fixed
- fixed missing translation phrases in the SEO module
- fixed the date format of the `lastmod` node inside XML sitemaps

## [4.3.2] - 2016-10-31
### Added
- [#12](https://github.com/ACP3/cms/issues/12) Added the `updated_at` database column to the following modules:
    - Articles
    - Files
    - Gallery
    - News
    - Newsletters
    - Polls

### Fixed
- fixed the editing of newsletters

## [4.3.1] - 2016-10-31
### Fixed
- fixed the SEO module's administration forms

## [4.3.0] - 2016-10-31
### Added
- [#42](https://github.com/ACP3/cms/issues/42) The SEO module is now able to automatically generate XML sitemaps.
- The following modules can now add their own URLs to the sitemap:
    - Articles
    - Contact
    - Files
    - Gallery
    - News
- The `ModelSaveEvent` class now accepts the raw post data too
- Applied the `RewriteUri` Smarty modifier to the article module's single article widget action 
- [#20](https://github.com/ACP3/cms/issues/20) Redesigned some parts of the gallery module's templates
- the template under `System/Partials/no_results.tpl` now accepts the parameter `no_results_text` to render a custom translation phrase 

### Changed
- unified all module extensions to be located under the same namespace of the specific module
- reworked the SEO URI alias saving logic to use the `core.model.after_save` event
- refactored the `SearchAvailabilityExtension` classes to reduce the code duplication

### Fixed
- fixed the wrong initial value of the `mailer_smtp_security` system config entry
- fixed `PictureRepository::getNextPictureId()` method 

## [4.2.0] - 2016-10-29
### Added
- Modules can now add service container compiler passes too

### Changed
- The captcha validation is now handled by the new event `captcha.validation.validate_captcha`
- moved the site title from the SEO module to the system module
- refactored the search module to use the new compiler pass functionality
- refactored the feeds module to use the new compiler pass functionality

### Fixed
- fixed the Smarty modifier plugin `PrefixUri` when there is an URL given with a valid protocol
- fixed the hardcoded feed links
- corrected the package information of the various `suggest` nodes inside the composer.json files 

## [4.1.30] - 2016-10-27
### Added
- [#39](https://github.com/ACP3/cms/issues/39) Added a new system config option which makes it possible to enable or disable the page cache
- made it possible to dispatch custom events to the validator to make the form validation much more flexible

### Changed
- made it possible to run the ACP3 without the SEO module
- the SEO form fields are now getting injected via a template event 

### Fixed
- fixed the page cache invalidation notification when in production mode
- fixed the newsletter subscription

## [4.1.29] - 2016-10-23
### Added
- Added the possibility to clear just the page cache
- Added the new template event `layout.content_before`
- Added the new event `core.settings.save_before` so that it is possible to modify the module settings before saving then to the database

### Changed
- When creating/modifying/deleting a result to the database, the page cache isn't cleared immediately anymore (for most operations)
- Improved the default layout of the ACP3

### Fixed
- fixed the users administration
- fixed the redirect url when posting a comment
- fixed the URL of delete controller action when performing a mass removal of comments
- fixed the language switcher drop down of the installer

## [4.1.28] - 2016-10-05
### Fixed
- Fixed the theme inheritance

## [4.1.27] - 2016-10-03
### Changed
- Reworked the version update check

## [4.1.22] - 2016-10-02
### Added
- Added a drop down menu to select the right controller action area when adding/editing a new resource
- Added a new deployment stage to Travis CI which uploads a build artifact to the Github releases

### Changed
- Made it possible to run the ACP3 without the ACP3/Modules/Custom folder

### Fixed
- The $IS_HOMEPAGE Smarty variable should now be always correct

## [4.1.21] - 2016-09-29
### Fixed
- Fixed the design path absolute and protected at against invalid values

## [4.1.20] - 2016-09-28
### Changed
- Dropped the usage of bower and use npm for all CSS and JS dependencies

### Fixed
- fixed the parent menu item selector when switching the menu block

## [4.1.19] - 2016-09-25
### Enhanced
- Refactored the Travis CI integration 

### Fixed
- Menus with different configuration parameters but the same menu index name should not collide anymore

## [4.1.18] - 2016-09-22
### Added
- CHANGELOG.md file

### Fixed
- It should be possible again to save menu items
- It should be possible again to save ACL roles
- Silenced a possible PHP warning when trying to login with incorrect credentials 

[Unreleased]: https://github.com/ACP3/cms/compare/v4.3.6...HEAD
[4.3.6]: https://github.com/ACP3/cms/compare/v4.3.5...v4.3.6
[4.3.5]: https://github.com/ACP3/cms/compare/v4.3.4...v4.3.5
[4.3.4]: https://github.com/ACP3/cms/compare/v4.3.3...v4.3.4
[4.3.3]: https://github.com/ACP3/cms/compare/v4.3.2...v4.3.3
[4.3.2]: https://github.com/ACP3/cms/compare/v4.3.1...v4.3.2
[4.3.1]: https://github.com/ACP3/cms/compare/v4.3.0...v4.3.1
[4.3.0]: https://github.com/ACP3/cms/compare/v4.2.0...v4.3.0
[4.2.0]: https://github.com/ACP3/cms/compare/v4.1.30...v4.2.0
[4.1.30]: https://github.com/ACP3/cms/compare/v4.1.29...v4.1.30
[4.1.29]: https://github.com/ACP3/cms/compare/v4.1.28...v4.1.29
[4.1.28]: https://github.com/ACP3/cms/compare/v4.1.27...v4.1.28
[4.1.27]: https://github.com/ACP3/cms/compare/v4.1.22...v4.1.27
[4.1.22]: https://github.com/ACP3/cms/compare/v4.1.21...v4.1.22
[4.1.21]: https://github.com/ACP3/cms/compare/v4.1.20...v4.1.21
[4.1.20]: https://github.com/ACP3/cms/compare/v4.1.19...v4.1.20
[4.1.19]: https://github.com/ACP3/cms/compare/v4.1.18...v4.1.19
[4.1.18]: https://github.com/ACP3/cms/compare/v4.1.17...v4.1.18
