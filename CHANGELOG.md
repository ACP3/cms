# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased] - 2016-xx-xx
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
- added a new system config option which makes it possible to enable or disable the page cache
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
