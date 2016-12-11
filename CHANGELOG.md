# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [4.4.4] - 20016-12-11
### Fixed
- Fixed the the inability to create guestbook entries or comments when not being logged in

## [4.4.3] - 20016-11-24
### Fixed
- Fixed the retrieval of the user context hash when the remember me cookie is set
- Fixed the URL to the installer when trying to call the ACP3 when it isn't installed

## [4.4.2] - 2016-11-17
### Added
- [#56](https://github.com/ACP3/cms/issues/56) Made the cache directory of the `ACP3\Core\Picture` class configurable
 
### Changed
- The cached pictures of the gallery module are now being generated in the uploads/gallery/cache folder 

## [4.4.1] - 2016-11-15
### Fixed
- Fixed the menu item management

## [4.4.0] - 2016-11-13
### Added
- [#49](https://github.com/ACP3/cms/issues/49) Added the ability to use a custom menu item title when creating a menu item via the articles module
- [#52](https://github.com/ACP3/cms/issues/52) Added a new system config option to select, whether the page cache is getting purged automatically or manually
- [#8](https://github.com/ACP3/cms/issues/8) Added the foundation to get module specific results per page
- Extended the contact module settings with the following new options:
    - Mobile phone
    - Picture credits

### Changed
- Deprecated the `UserModel::getEntriesPerPage()` and `UserModel::setEntriesPerPage()` methods
- Extracted the adding of the data grid columns into separate methods
- Updated the [mibe/feedwriter](https://github.com/mibe/FeedWriter) library to version v1.1.0
- [#55](https://github.com/ACP3/cms/pull/55) Changed the argument order of the \FeedWriter\Feed::setImage() method (thanks @mibe)
- [#54](https://github.com/ACP3/cms/issues/54) Moved the password form fields from the users account profile action to the user account settings action
- The contact module settings have been moved into its own controller action to match the structure of the other modules 
- The feeds module settings have been moved into its own controller action to match the structure of the other modules 

### Fixed
- Fixed the pictures count of the gallery data grid
- When switching the design, purge the following folders to prevent from corrupted layouts after the page reload:
    - `cache/env/sql`
    - `cache/env/tpl_compiled`
    - `cache/env/http`

## [4.3.6] - 2016-11-03
### Added
- Automatically create the uploads/assets directory if it is missing
- Zhe `\ACP3\Core\Cache\Purge::purgeCurrentDirectory()` method now handles symbolic links gracefully too

### Fixed
- [#46](https://github.com/ACP3/cms/issues/46) Fixed the disappearing file size unit when editing a download

## [4.3.5] - 2016-11-02
### Changed
- Improved the performance of the `\ACP3\Core\Cache\Purge::doPurge()` method 

### Fixed
- Fixed a character encoding problem when trying to use formatted HTML/XML code with the WYSIWYG-Editor inputs
- Minor fixes for the default frontend templates of the gallery and files modules

## [4.3.4] - 2016-11-02
### Changed
- Refined the default ACP3 design

### Fixed
- Fixed the missing gallery title when saving a gallery

## [4.3.3] - 2016-11-01
### Fixed
- Fixed missing translation phrases in the SEO module
- Fixed the date format of the `lastmod` node inside XML sitemaps

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
- Fixed the editing of newsletters

## [4.3.1] - 2016-10-31
### Fixed
- Fixed the SEO module's administration forms

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
- The template under `System/Partials/no_results.tpl` now accepts the parameter `no_results_text` to render a custom translation phrase 

### Changed
- Unified all module extensions to be located under the same namespace of the specific module
- Reworked the SEO URI alias saving logic to use the `core.model.after_save` event
- Refactored the `SearchAvailabilityExtension` classes to reduce the code duplication

### Fixed
- Fixed the wrong initial value of the `mailer_smtp_security` system config entry
- Fixed `PictureRepository::getNextPictureId()` method 

## [4.2.0] - 2016-10-29
### Added
- Modules can now add service container compiler passes too

### Changed
- The captcha validation is now handled by the new event `captcha.validation.validate_captcha`
- Moved the site title from the SEO module to the system module
- Refactored the search module to use the new compiler pass functionality
- Refactored the feeds module to use the new compiler pass functionality

### Fixed
- Fixed the Smarty modifier plugin `PrefixUri` when there is an URL given with a valid protocol
- Fixed the hardcoded feed links
- Corrected the package information of the various `suggest` nodes inside the composer.json files 

## [4.1.30] - 2016-10-27
### Added
- [#39](https://github.com/ACP3/cms/issues/39) Added a new system config option which makes it possible to enable or disable the page cache
- Made it possible to dispatch custom events to the validator to make the form validation much more flexible

### Changed
- Made it possible to run the ACP3 without the SEO module
- The SEO form fields are now getting injected via a template event 

### Fixed
- Fixed the page cache invalidation notification when in production mode
- Fixed the newsletter subscription

## [4.1.29] - 2016-10-23
### Added
- Added the possibility to clear just the page cache
- Added the new template event `layout.content_before`
- Added the new event `core.settings.save_before` so that it is possible to modify the module settings before saving then to the database

### Changed
- When creating/modifying/deleting a result to the database, the page cache isn't cleared immediately anymore (for most operations)
- Improved the default layout of the ACP3

### Fixed
- Fixed the users administration
- Fixed the redirect url when posting a comment
- Fixed the URL of delete controller action when performing a mass removal of comments
- Fixed the language switcher drop down of the installer

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
- Fixed the parent menu item selector when switching the menu block

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

[Unreleased]: https://github.com/ACP3/cms/compare/v4.4.3...HEAD
[4.4.3]: https://github.com/ACP3/cms/compare/v4.4.2...v4.4.3
[4.4.2]: https://github.com/ACP3/cms/compare/v4.4.1...v4.4.2
[4.4.1]: https://github.com/ACP3/cms/compare/v4.4.0...v4.4.1
[4.4.0]: https://github.com/ACP3/cms/compare/v4.3.6...v4.4.0
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
