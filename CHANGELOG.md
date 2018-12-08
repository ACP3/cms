# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Fixed
- fixed a bug when patching an existing DB result using the *Model classes

## [4.33.4] - 2018-12-07
### Changed
- allow Symfony 4.2.x components again, as version 4.2.1 fixed the issues the container
- made the building of the URI aliases a little bit more smarter

### Fixed
- fixed the links of the pagination if it was displayed on the homepage
- fixed an incorrect SQL query of the news module

## [4.33.3] - 2018-12-05
### Fixed
- fixed some more bugs related to MySQL/MariaDB servers configured in TRADITIONAL sql_mode + ONLY_FULL_GROUP_BY
- fixed a minor styling bug in the menu items data grid

## [4.33.2] - 2018-12-01
### Changed
- restricted the symfony components to version 4.1.x so that the ACP3 doesn't break

## [4.33.1] - 2018-11-30
### Added
- added support for the new (much more stricter) configuration for MySQL version > 5.7.4
- added a simple docker-compose development environment

## [4.33.0] - 2018-11-25
### Changed
- Changed the establishing of the connection to the database to be lazy 
- Changed the loading of Smarty plugins to be lazy
- Changed the loading of validation rules inside the Validator class to be lazy
- Changed the loading of column renderers inside the DataGrid class to be lazy

### Fixed
- when an erroneously generated breadcrumb when paginating an article 

## [4.32.1] - 2018-11-21
### Added
- Added PHPStan as a static analysis tool

### Fixed
- Fixed the sort direction of the SortColumnRenderer when using the data grids in AJAX mode
- do not scroll to the first form error if the AJAX form is inside a modal

## [4.32.0] - 2018-11-07
### Changed
- Improved the error handling when the ajax-form.js is used
- Separated the action buttons within the data grid so that they can be clicked much more easily

## [4.31.1] - 2018-10-21
### Fixed
- fallback to PHP's standard `getimagesize()` function if the FastImageSize library fails to retrieve the information of a picture

## [4.31.0] - 2018-10-21
### Added
- added the possibility to add a description to galleries
- added the possibility to add a title to gallery pictures
- added the possibility to move existing gallery pictures to an other gallery

## [4.30.4] - 2018-10-20
### Fixed
- fixed a possible JavaScript bug when reCaptcha was activated, but no captcha was displayed on the current page
- fixed the guestbook modal form submission using the AJAX form
- improved sorting of the entries within the files data grid

## [4.30.3] - 2018-10-04
### Fixed
- fixed a possible TypeError with the `ACP3\Core\Picture\Output` class

## [4.30.2] - 2018-10-04
### Fixed
- fixed a possible TypeError when creating a download

## [4.30.0] - 2018-10-03
### Added
- Extended the audit log module
- back ported the nested categories from ACP3.next
- added and used the possibility to retrieve the results for the data grids via AJAX 

### Changes
- reworked how ACL rules are getting updated
- introduce `DataGridRepository` classes for the following modules:
    - Comments
    - Gallery pictures
- updated the `pixeldeveloper/sitemap` package to version 5.x

### Minor BC breaks
- Changed the signature of the `AbstractSitemapAvailabilityExtension::addUrl()` method to only accept `\DateTimeInterface` objects for the `$lastModificationDate` argument

### Deprecations (to be removed with version 5.0.0)
- Reworked the Smarty plugins:
  - Removed the class `ACP3\Core\View\Renderer\Smarty\AbstractPlugin`
  - Deprecated the method `ACP3\Core\View\Renderer\Smarty\PluginInterface::getExtensionType()` and all its implementations
  - Deprecated the constants inside `ACP3\Core\View\Renderer\Smarty\PluginInterface`, use class `ACP3\Core\View\Renderer\Smarty\PluginTypeEnum` instead
  - Deprecated the method `ACP3\Core\View\Renderer\Smarty\Blocks\AbstractBlock::process()`, use `ACP3\Core\View\Renderer\Smarty\Blocks\AbstractBlock::__invoke()` instead 
  - Deprecated the method `ACP3\Core\View\Renderer\Smarty\Filters\AbstractFilter::process()`, use `ACP3\Core\View\Renderer\Smarty\Filters\AbstractFilters::__invoke()` instead 
  - Deprecated the method `ACP3\Core\View\Renderer\Smarty\Functions\AbstractFunction::process()`, use `ACP3\Core\View\Renderer\Smarty\Functions\AbstractFunctions::__invoke()` instead 
  - Deprecated the method `ACP3\Core\View\Renderer\Smarty\Modifiers\AbstractModifier::process()`, use `ACP3\Core\View\Renderer\Smarty\Modifiers\AbstractModifiers::__invoke()` instead
- Deprecated the class `ACP3\Core\Model\AbstractNestedSetModel`, use `ACP3\Core\NestedSet\Model\AbstractNestedSetModel` instead
- Deprecated the class `ACP3\Core\Picture`, use `ACP3\Core\Picture\Picture` instead
- Deprecated the method `ACP3\Core\Mailer::setData()`, use the method `ACP3\Core\Mailer::send()` to inject the `MailerMessage` directly
- Deprecated the class `ACP3\Core\Model\Repository\DataGridRepository`, use `ACP3\Core\DataGrid\Model\Repository\AbstractDataGridRepository` instead
- Deprecated the namespace `ÀCP3\Core\Helpers\DataGrid*`, use `ÀCP3\Core\DataGrid*` instead
- Deprecated the DI container tag `core.helpers.data_grid.column_renderer`, use `core.data_grid.column_renderer` instead

### Fixes
- Fixed the default sort order of the gallery pictures

## [4.29.4] - 2018-09-05
### Fixed
- removed the ext-* nodes from the composer.json

## [4.29.3] - 2018-09-05
### Fixed
- fixed the PHPUnit version of the `acp3/test` package
- unsure, that we always use the correct file paths after the image resizing

## [4.29.2] - 2018-09-04
### Fixed
- fixed the legacy image controller action to not break the hole application

## [4.29.1] - 2018-09-04
### Fixed
- fixed the `ACP3\Core\Environment\Theme::getDesignPathWeb()` method so that it returns the correct path
- the CKEditor now preserves empty `<i>`-tags, as they are mostly used for icons

## [4.29.0] - 2018-09-03
### Added
- back ported the possibility to run CLI commands from ACP3.next
- back ported the honeypot captcha from ACP3.next

### Changed
- Sort the frontend libraries in topological order
- Reworked the caching of the gallery pictures
- Converted the event listeners to invokable classes
- Updated PHPUnit to version 7.3
- Updated the Symfony DI container to version 4.1
- Updated the Symfony HTTP kernel to version 4.1

## [4.28.4] - 2018-08-20
### Fixed
- Fixed the installation and update wizard

## [4.28.0] - 2018-08-19
### Changed
- Converted all database tables and columns to the `utf8mb4` charset
- Replaced our own ErrorHandler with the ErrorHandler and ExceptionHandler of the [symfony/debug](https://packagist.org/packages/symfony/debug) package
- Marked all necessary services public
- Reworked the triggering of various actions during the bootstrap process
- Updated jQuery to version 3.3.1
- Updated gulp to version 4.0.0
- Target EcmaScript version 6 for our own JavaScript code

### Fixed
- fixed the file upload when creating a new download

## [4.27.1] - 2018-07-11
### Changed
- Updated CKEditor to version 4.10
- Updated TinyMCE to version 4.8

## Fixed
- Fixed the bug when creating/editing an article a menu item get's accidentally created too

## [4.27.0] - 2018-04-02
### Added
- made it possible to override module translation files using the currently activated theme (including the theme inheritance)
- it is now possible to use deeply nested folder structures for templates and other static assets

### Changed
- many code optimizations which should reduce the pressure on the file system

## [4.26.4] - 2018-03-28
### Fixed
- Correctly reset the reCAPTCHA if the AJAX form submission failed
- fixed the guestbook AJAX form submission
- fixed the gallery picture delete action
- fixed the gallery picture edit action

## [4.26.2] - 2018-03-22
### Changed
- removed the old assets of fontawesome 4.7

### Fixed
- fixed an edge case when trying to retrieve an URI alias

## [4.26.1] - 2018-03-19
### Added
- added the solid icons of fontawesome 5

## [4.26.0] - 2018-03-19
### Changed
- updated fontawesome to version 5
- updated shariff to version 3.0.0
- when generating a route with the path of homepage, it simple will return `/`

## [4.25.7] - 2018-03-19
### Changed
- added the CSS selector `.cookie_consent__body` to improve the styleability of the cookie consent

### Fixed
- fixed the missing horizontal line when only the social sharing (without ratings) was active
- various minor bug fixes and improvements for the social sharing module

## [4.25.6] - 2018-03-18
### Fixed
- code styles fixes

## [4.25.5] - 2018-03-18
### Fixed
- fixed a possible 500 error on pages where the social sharing should be included but the module is not installed at all

## [4.25.4] - 2018-03-16
### Fixed
- fixed a possible undefined index error

## [4.25.3] - 2018-03-16
### Fixed
- code styles fixes

## [4.25.2] - 2018-03-16
### Fixed
- the `rate` controller action of the social sharing module inherited from the wrong `AbstractFrontendAction`

## [4.25.1] - 2018-03-16
### Fixed
- fixed a bug when initially saving the to be activated social services

## [4.25.0] - 2018-03-16
### Added
- added a new module: Social sharing using the shariff library
- added font-awesome as new asset library (it is disabled by default)
- made it possible to override all available settings of the AJAX-forms jQuery plugin with `data-` attributes

### Changed
- changed the AJAX-forms (ajax-form.js) be act as a library and it is now loaded by default
- the JS cookie library is not loaded by default anymore
- reworked the view initialization

### Fixed
- when adding an asset library which is enabled by default,
  the dependencies will now also get enabled
- fixed a regression introduced with last release regarding the breadcrumb steps

## [4.24.0] - 2018-03-10
### Added
- made it possible to programmatically remove steps from the breadcrumb
- added the `core.breadcrumb.steps.build_frontend_cache_after` event
- added the `menus.breadcrumb.steps.build_frontend_cache_after` event

### Changed
- moved the `core.breadcrumb.steps.build_admin_cache_not_empty_steps_after` event to a better location
- removed the `core.breadcrumb.steps.build_admin_cache_empty_steps_before` event

## [4.23.0] - 2018-03-01
### Added
- added the possibility to define a subtitle for an article

### Changed
- extended the maximum length of an article title to 255 characters

## [4.22.1] - 2018-02-23
### Fixed
- Fixed a bug when trying to save a menu item

## [4.22.0] - 2018-02-22
### Added
- Added a new module: Auditlog. This module keeps track of all the changes users are doing to the database resultsets. For now, there are no user visible features.
- It is now possible to comfortably select a custom layout for an article

### Changed
- Updated the composer dependencies
- Updated the npm dependencies
- Modules are now getting loaded in topological sort order
- Module schemas are now getting updated in topological sort order
- Updated the CKEditor to version 4.8.x
- Extended the capabilities of the AbstractModel to detect if the to be saved result has any changed data or not

### Fixed
- Fixed a bug within the permission system when a role was inheriting certain values from its parent role
- Fixed a possible PHP error if the version update checks fails

## [4.21.2] - 2018-01-20
### Changed
- the access_forbidden and not_found actions of the errors module are now using the "normal" layout, to improve the UX in case of these errors

## [4.21.1] - 2018-01-20
### Fixed
- Added a missing composer dependency to the ACP3 core module
- Fixed the apidocs post deployment CI job

## [4.21.0] - 2018-01-19
### Added
- the AJAX form class is a little bit smarter when dealing with location hash changes and tabs

### Changed
- Moved most of the CI tasks from Travis to Gitlab.com
- Reworked and extended the CI/CD pipeline
- Run php-cs-fixer and ESLint as part of the CI/CD pipeline
- The unit tests are now getting exported, but are not getting added to the classmap autoloader
- Updated the SPDX license identifiers to be compatible with version 3.0

## [4.20.0] - 2017-12-21
### Changed
- Updated the friendsofsymfony/http-cache package to version 2.1.0
- Updated the symfony components to version 3.4.2
- Updated moment.js to version 2.20.1

## [4.19.1] - 2017-12-16
### Added
- Added a template file for the logout page

### Changed
- Correctly vary the HTTP cache by the X-User-Context-Hash and not by Cookie

### Fixed
- Fixed the custom meta title when being used via the SEO module

## [4.19.0] - 2017-12-13
### Changed
- Changed the defaults of the pagination:
  - Always show the next/previous buttons
  - Reduced the to be displayed pages from 7 to 3
- Improved the default template of the pagination to be more accessible
- Reworked the breadcrumb so that the structure from the menus take precedence
- Updated the FOSHttpCache composer package to version 2.*
- Updated the slugify composer package to version 3.*
- Updated the PHPMailer composer package to version 6.*
- Dropped the bower.json

## [4.18.0] - 2017-11-22
### Added
- Added self referencing canonical URLs to the SEO module 

### Changed
- Dropped the support for PHP 5.6. The minimum required PHP version is 7.1 now
- Dropped the support for HHVM
- Updated Symfony to version 3.3
- Updated the bundled JavaScript packages to their current versions

### Fixed
- Fixed the position of the charset meta tag inside the HTML <head>

## [4.17.0] - 2017-11-12
### Added
- Added the new data grid column renderer `RouteColumnRenderer` which makes it possible specify and open routes in new tabs

### Changed
- Decoupled the menu item management from the articles module some more

### Fixed
- Fixed the bug that it was not possible to create new articles when the menu items module was installed and active, but there were no menus at all
- [#70](https://gitlab.com/ACP3/cms/issues/70) Fixed the sitemap generation with present, but not installed modules 

## [4.16.0] - 2017-10-24
### Added
- Added a new option which completely disables the site subtitle program logic 

### Changed
- extracted the gallery pictures data grid into a separate controller action

## [4.15.0] - 2017-10-23
### Added
- Added the schema.org `BreadcrumbList` markup to the breadcrumb template file 
- Added the possibility to add a separate page title via the SEO form fields
- Added the new Smarty function `site_subtitle` which exposes the site's subtitle to the frontend 
- When inside the administration and creating/editing a resultset, you can now choose between `save and close` and `save and continue`

### Changed
- When deleting a category, the associated news or downloads won't be deleted anymore

## [4.14.0] - 2017-10-22
### Changed
- Added an explicit breadcrumb to the imprint controller action of the contact module, as the imprint is not directly associated with the contact form
- The titles of the programmatically breadcrumb steps now always take precedence over the ones from the database
- When the homepage is being displayed, set the canonical URL to the website root
- Display an alert when an user tries to access a restricted pages which requires the user to be logged in

## [4.13.1] - 2017-10-21
### Fixed
- Fixed the inability to create a new menu item which should not be displayed
- Fixed a bug with the breadcrumbs where the breadcrumbs were sometimes not right when being used in conjunction with the menus module 
- Fixed the bug that the `parent_id`s of the node's siblings were wrong, when the node was a root node after deleting it

## [4.13.0] - 2017-09-01
### Added
- Added some CLI scripts to the composer.json

### Changed
- Adjusted the modules composer.json files so that the version constraint for the `acp3/composer-installer` package is `^1.0` and not `*`

## [4.12.2] - 2017-08-16
### Fixed
- Correctly escape the special HTML characters, so that the Email sending isn't breaking

## [4.12.1] - 2017-08-14
### Fixed
- Fixed a BC break inside the `Mailer` class which came with the introduction of the `MailerMessage` class

## [4.12.0] - 2017-05-08
### Added
- Added the possibility to duplicate downloads via the admin data grid
- Added the possibility to quickly enable/disable downloads
- Added the possibility to sort downloads by date or with an custom order
- [#62](https://gitlab.com/ACP3/cms/issues/62) Added an alert message when the currently used ACP3 CMS is outdated

### Deprecations
- Deprecated `\ACP3\Core\Controller\AbstractAdminAction`, use `\ACP3\Core\Controller\AbstractFrontendAction` instead

### Changed
- Updated `giggsey/locale` library to version 1.3 to take advantage of the new functionality for the localized country list

### Fixed
- Fixed the creation/editing of categories via the admin panel

## [4.11.1] - 2017-04-07
### Fixed
- Fixed the required version of the minify library of the ACP3 core framework

## [4.11.0] - 2017-04-07
### Added
- Added the possibility to duplicate articles via the admin data grid
- Added the possibility to duplicate news via the admin data grid
- Added the possibility to quickly enable/disable articles
- Added the possibility to quickly enable/disable news
- Added the possibility to the SEO module to index all pages or only the first page of paginated content
- When the AJAX form validation has previously failed and the user alters an affected form element, the validation is triggered automatically again
- Added new options to the systems settings for managing the site title

### Changed
- Rearranged some systems settings into new tabs
- Renamed the system configuration to system settings so that it aligns with all other module settings actions
- Updated fancybox to version 3.0.47
- Updated minify to version 3.0

### Fixed
- Fixed the gallery picture upload when the SEO module is active 

## [4.10.1] - 2017-03-30
### Fixed
- Fixed the deployment

## [4.10.0] - 2017-03-30
### Added
- Added the library `fisharebest/localization` which handles the retrieval of the output of the localized name of a language pack and the script direction
- Added the new trait `AvailableDesignsTrait` which makes it possible to share the design retrieval logic between the installer and the rest of the system
- Made it possible to use the AJAX-form hash change logic for redirects, too
- Added the possibility to set a reply-to address or sender address to the `Mailer` class

### Changed
- Added some guards to various modules to make them more robust 
- Replaced the library `umpirsky/country-list` with `giggsey/locale` because of its significantly reduced storage footprint 

## [4.9.2] - 2017-03-26
### Fixed
- Fixed the `uri` Smarty function

## [4.9.1] - 2017-03-26
### Changed
- some minor optimizations for the .htaccess file

## [4.9.0] - 2017-03-26
### Added
- Extended the router so that it's possible to force the generation of HTTP URIs
- Added the possibility to generate separate XML-sitemaps for HTTP and HTTPS
- Made it possible to use modules without the need for an installation

### Changed
- marked all services which are used as part of a bigger component as `public: false`
- renamed some services to that a better aligned with their counterparts
- refactored the installation of modules

## [4.8.5] - 2017-03-19
### Fixed
- Fixed the attachment handling of the `Mailer` when using the new `MailerMessage` class

## [4.8.3] - 2017-03-19
### Fixed
- Fixed the AJAX form handling when there has been an form validation error

## [4.8.2] - 2017-03-18
### Fixed
- Various minor fixes and improvements

## [4.8.1] - 2017-03-18
### Fixed
- Fixed the captcha module's composer.json schema

## [4.8.0] - 2017-03-18
### Added
- Updated the IncludeJs Smarty function to append a query string with the current version of the ACP3 for HTTP cache busting
- Added reCAPATCHA as a new captcha type
- Added the new Smarty function "image" which makes it possible to include an image from the Assets/img folder of a design
- Added theme inheritance based HTML email layouts for the contact, newsletter and users module

### Changed
- Refactored the captcha system so that it can be extended with different captcha types
- Refactored the URI alias generation for gallery pictures into an event
- Improved the page titles when editing entries via the admin panel to make them more easily identifiable

### Fixed
- Fixed the picture number generation when adding a new gallery picture
- Fixed the forgot password action of the users module

## [4.7.1] - 2017-02-27
### Fixed
- fixed the AJAX forms

## [4.7.0] - 2017-02-27
### Added
- added the cookie consent to the system configuration
- added several table indexes to improve the performance with large databases
- extended the capabilities of the contact module with the ability of persisting the contact form messages into a database table
- the `Upload::moveFile()` method now attempts to create the desired upload folder by itself if it does not already exist
- the `RewriteInternalUri()` class can now rewrite inline URIs too

### Changed
- reworked the form error handling when performing AJAX requests
- the submit button, which has triggered the AJAX request, gets disabled now to prevent from submitting the form twice

## [4.6.2] - 2017-02-25
### Fixed
- Added the umpirsky/country-list library to the acp3/core package

## [4.6.1] - 2017-02-25
### Fixed
- Fixed a wrongly referenced service name in the permissions module's models

## [4.6.0] - 2017-02-25
### Added
- added the umpirsky/country-list lib, so that we have a localized list of the world countries
- added the possibility to add additional HTML attributes to the form_group.input_*.tpl partials
- added the possibility to use input-groups for the form_group.input_*.tpl based partials 
- added the following two new template events to the users account index template
    - `users.account.index.header_bar`
    - `user.account.index.dashboard`
- reworked the ajax-form jQuery plugin so that it is possible to execute callbacks after a successful AJAX request

### Changed
- Removed the user specific short and long date formats, time zone and language because of low usage
- do not hide the loading layer when getting redirected to another URL after performing an AJAX request
- made the session settings a little bit more secure
- run the session garbage collection with a probability of 1% instead of 10%

### Fixed
- Fixed the SMTP mailer validation in the system settings

## [4.5.0] - 2017-01-15
### Security
- Updated the PHPMailer library to version 5.2.22 to fix various security issues

### Added
- Extended the ajax-form jQuery plugin with the ability to gracefully handle failed AJAX requests
- Extended the ModelSaveEvent with the ability to determine, whether the saved result is actually new or an existing result has been saved

### Changed
- Reworked and improved the form handling a little bit
- Updated the CKEditor WYSIWYG-Editor to version 4.6
- Unified the `\ACP3\Core\Helper\Action::handleCreatePostAction()` and `\ACP3\Core\Helper\Action::handleEditPostAction()` methods into the new method `\ACP3\Core\Helper\Action::handleSaveAction()`
- added an alternate syntax for the load_module Smarty function

### Fixed
- Fixed the deleting of the category picture when deleting a category
- Fixed the deleting of the assigned menu item and SEO settings when deleting an article
- Fixed the `dropdown` CSS selector name when generating a bootstrap enabled menu
- Fixed the newsletter subscription via the newsletter widget

### Deprecations
- Deprecated `\ACP3\Core\Controller\Context\AdminContext`, use `\ACP3\Core\Controller\Context\FrontendContext` instead
- Deprecated `\ACP3\Core\Helper\Action::handleCreatePostAction()`, use `\ACP3\Core\Helper\Action::handleSaveAction()` instead
- Deprecated `\ACP3\Core\Helper\Action::handleEditPostAction()`, use `\ACP3\Core\Helper\Action::handleSaveAction()` instead

## [4.4.4] - 20016-12-11
### Fixed
- Fixed the the inability to create guestbook entries or comments when not being logged in

## [4.4.3] - 20016-11-24
### Fixed
- Fixed the retrieval of the user context hash when the remember me cookie is set
- Fixed the URL to the installer when trying to call the ACP3 when it isn't installed

## [4.4.2] - 2016-11-17
### Added
- [#56](https://gitlab.com/ACP3/cms/issues/56) Made the cache directory of the `ACP3\Core\Picture` class configurable
 
### Changed
- The cached pictures of the gallery module are now being generated in the uploads/gallery/cache folder 

## [4.4.1] - 2016-11-15
### Fixed
- Fixed the menu item management

## [4.4.0] - 2016-11-13
### Added
- [#49](https://gitlab.com/ACP3/cms/issues/49) Added the ability to use a custom menu item title when creating a menu item via the articles module
- [#52](https://gitlab.com/ACP3/cms/issues/52) Added a new system config option to select, whether the page cache is getting purged automatically or manually
- [#8](https://gitlab.com/ACP3/cms/issues/8) Added the foundation to get module specific results per page
- Extended the contact module settings with the following new options:
    - Mobile phone
    - Picture credits

### Changed
- Deprecated the `UserModel::getEntriesPerPage()` and `UserModel::setEntriesPerPage()` methods
- Extracted the adding of the data grid columns into separate methods
- Updated the [mibe/feedwriter](https://github.com/mibe/FeedWriter) library to version v1.1.0
- [#55](https://gitlab.com/ACP3/cms/pull/55) Changed the argument order of the \FeedWriter\Feed::setImage() method (thanks @mibe)
- [#54](https://gitlab.com/ACP3/cms/issues/54) Moved the password form fields from the users account profile action to the user account settings action
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
- [#46](https://gitlab.com/ACP3/cms/issues/46) Fixed the disappearing file size unit when editing a download

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
- [#12](https://gitlab.com/ACP3/cms/issues/12) Added the `updated_at` database column to the following modules:
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
- [#42](https://gitlab.com/ACP3/cms/issues/42) The SEO module is now able to automatically generate XML sitemaps.
- The following modules can now add their own URLs to the sitemap:
    - Articles
    - Contact
    - Files
    - Gallery
    - News
- The `ModelSaveEvent` class now accepts the raw post data too
- Applied the `RewriteUri` Smarty modifier to the article module's single article widget action 
- [#20](https://gitlab.com/ACP3/cms/issues/20) Redesigned some parts of the gallery module's templates
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
- [#39](https://gitlab.com/ACP3/cms/issues/39) Added a new system config option which makes it possible to enable or disable the page cache
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

[Unreleased]: https://gitlab.com/ACP3/cms/compare/v4.33.4...4.x
[4.33.4]: https://gitlab.com/ACP3/cms/compare/v4.33.3...v4.33.4
[4.33.3]: https://gitlab.com/ACP3/cms/compare/v4.33.2...v4.33.3
[4.33.2]: https://gitlab.com/ACP3/cms/compare/v4.33.1...v4.33.2
[4.33.1]: https://gitlab.com/ACP3/cms/compare/v4.33.0...v4.33.1
[4.33.0]: https://gitlab.com/ACP3/cms/compare/v4.32.1...v4.33.0
[4.32.1]: https://gitlab.com/ACP3/cms/compare/v4.32.0...v4.32.1
[4.32.0]: https://gitlab.com/ACP3/cms/compare/v4.31.1...v4.32.0
[4.31.1]: https://gitlab.com/ACP3/cms/compare/v4.31.0...v4.31.1
[4.31.0]: https://gitlab.com/ACP3/cms/compare/v4.30.4...v4.31.0
[4.30.4]: https://gitlab.com/ACP3/cms/compare/v4.30.3...v4.30.4
[4.30.3]: https://gitlab.com/ACP3/cms/compare/v4.30.2...v4.30.3
[4.30.2]: https://gitlab.com/ACP3/cms/compare/v4.30.1...v4.30.2
[4.30.1]: https://gitlab.com/ACP3/cms/compare/v4.30.0...v4.30.1
[4.30.0]: https://gitlab.com/ACP3/cms/compare/v4.29.4...v4.30.0
[4.29.4]: https://gitlab.com/ACP3/cms/compare/v4.29.3...v4.29.4
[4.29.3]: https://gitlab.com/ACP3/cms/compare/v4.29.2...v4.29.3
[4.29.2]: https://gitlab.com/ACP3/cms/compare/v4.29.1...v4.29.2
[4.29.1]: https://gitlab.com/ACP3/cms/compare/v4.29.0...v4.29.1
[4.29.0]: https://gitlab.com/ACP3/cms/compare/v4.28.4...v4.29.0
[4.28.4]: https://gitlab.com/ACP3/cms/compare/v4.28.3...v4.28.4
[4.28.3]: https://gitlab.com/ACP3/cms/compare/v4.28.2...v4.28.3
[4.28.2]: https://gitlab.com/ACP3/cms/compare/v4.28.1...v4.28.2
[4.28.1]: https://gitlab.com/ACP3/cms/compare/v4.28.0...v4.28.1
[4.28.0]: https://gitlab.com/ACP3/cms/compare/v4.27.3...v4.28.0
[4.27.3]: https://gitlab.com/ACP3/cms/compare/v4.27.2...v4.27.3
[4.27.2]: https://gitlab.com/ACP3/cms/compare/v4.27.1...v4.27.2
[4.27.1]: https://gitlab.com/ACP3/cms/compare/v4.27.0...v4.27.1
[4.27.0]: https://gitlab.com/ACP3/cms/compare/v4.26.4...v4.27.0
[4.26.4]: https://gitlab.com/ACP3/cms/compare/v4.26.2...v4.26.4
[4.26.2]: https://gitlab.com/ACP3/cms/compare/v4.26.1...v4.26.2
[4.26.1]: https://gitlab.com/ACP3/cms/compare/v4.26.0...v4.26.1
[4.26.0]: https://gitlab.com/ACP3/cms/compare/v4.25.7...v4.26.0
[4.25.7]: https://gitlab.com/ACP3/cms/compare/v4.25.6...v4.25.7
[4.25.6]: https://gitlab.com/ACP3/cms/compare/v4.25.5...v4.25.6
[4.25.5]: https://gitlab.com/ACP3/cms/compare/v4.25.4...v4.25.5
[4.25.4]: https://gitlab.com/ACP3/cms/compare/v4.25.3...v4.25.4
[4.25.3]: https://gitlab.com/ACP3/cms/compare/v4.25.2...v4.25.3
[4.25.2]: https://gitlab.com/ACP3/cms/compare/v4.25.1...v4.25.2
[4.25.1]: https://gitlab.com/ACP3/cms/compare/v4.25.0...v4.25.1
[4.25.0]: https://gitlab.com/ACP3/cms/compare/v4.24.0...v4.25.0
[4.24.0]: https://gitlab.com/ACP3/cms/compare/v4.23.0...v4.24.0
[4.23.0]: https://gitlab.com/ACP3/cms/compare/v4.22.1...v4.23.0
[4.22.1]: https://gitlab.com/ACP3/cms/compare/v4.22.0...v4.22.1
[4.22.0]: https://gitlab.com/ACP3/cms/compare/v4.21.2...v4.22.0
[4.21.2]: https://gitlab.com/ACP3/cms/compare/v4.21.1...v4.21.2
[4.21.1]: https://gitlab.com/ACP3/cms/compare/v4.21.0...v4.21.1
[4.21.0]: https://gitlab.com/ACP3/cms/compare/v4.20.0...v4.21.0
[4.20.0]: https://gitlab.com/ACP3/cms/compare/v4.19.1...v4.20.0
[4.19.1]: https://gitlab.com/ACP3/cms/compare/v4.19.0...v4.19.1
[4.19.0]: https://gitlab.com/ACP3/cms/compare/v4.18.0...v4.19.0
[4.18.0]: https://gitlab.com/ACP3/cms/compare/v4.17.0...v4.18.0
[4.17.0]: https://gitlab.com/ACP3/cms/compare/v4.16.0...v4.17.0
[4.16.0]: https://gitlab.com/ACP3/cms/compare/v4.15.0...v4.16.0
[4.15.0]: https://gitlab.com/ACP3/cms/compare/v4.14.0...v4.15.0
[4.14.0]: https://gitlab.com/ACP3/cms/compare/v4.13.1...v4.14.0
[4.13.1]: https://gitlab.com/ACP3/cms/compare/v4.13.0...v4.13.1
[4.13.0]: https://gitlab.com/ACP3/cms/compare/v4.12.2...v4.13.0
[4.12.2]: https://gitlab.com/ACP3/cms/compare/v4.12.1...v4.12.2
[4.12.1]: https://gitlab.com/ACP3/cms/compare/v4.12.0...v4.12.1
[4.12.0]: https://gitlab.com/ACP3/cms/compare/v4.11.1...v4.12.0
[4.11.1]: https://gitlab.com/ACP3/cms/compare/v4.11.0...v4.11.1
[4.11.0]: https://gitlab.com/ACP3/cms/compare/v4.10.1...v4.11.0
[4.10.1]: https://gitlab.com/ACP3/cms/compare/v4.10.0...v4.10.1
[4.10.0]: https://gitlab.com/ACP3/cms/compare/v4.9.2...v4.10.0
[4.9.2]: https://gitlab.com/ACP3/cms/compare/v4.9.1...v4.9.2
[4.9.1]: https://gitlab.com/ACP3/cms/compare/v4.9.0...v4.9.1
[4.9.0]: https://gitlab.com/ACP3/cms/compare/v4.8.5...v4.9.0
[4.8.5]: https://gitlab.com/ACP3/cms/compare/v4.8.3...v4.8.5
[4.8.3]: https://gitlab.com/ACP3/cms/compare/v4.8.2...v4.8.3
[4.8.2]: https://gitlab.com/ACP3/cms/compare/v4.8.1...v4.8.2
[4.8.1]: https://gitlab.com/ACP3/cms/compare/v4.8.0...v4.8.1
[4.8.0]: https://gitlab.com/ACP3/cms/compare/v4.7.1...v4.8.0
[4.7.1]: https://gitlab.com/ACP3/cms/compare/v4.7.0...v4.7.1
[4.7.0]: https://gitlab.com/ACP3/cms/compare/v4.6.2...v4.7.0
[4.6.2]: https://gitlab.com/ACP3/cms/compare/v4.6.1...v4.6.2
[4.6.1]: https://gitlab.com/ACP3/cms/compare/v4.6.0...v4.6.1
[4.6.0]: https://gitlab.com/ACP3/cms/compare/v4.5.0...v4.6.0
[4.5.0]: https://gitlab.com/ACP3/cms/compare/v4.4.4...v4.5.0
[4.4.4]: https://gitlab.com/ACP3/cms/compare/v4.4.3...v4.4.4
[4.4.3]: https://gitlab.com/ACP3/cms/compare/v4.4.2...v4.4.3
[4.4.2]: https://gitlab.com/ACP3/cms/compare/v4.4.1...v4.4.2
[4.4.1]: https://gitlab.com/ACP3/cms/compare/v4.4.0...v4.4.1
[4.4.0]: https://gitlab.com/ACP3/cms/compare/v4.3.6...v4.4.0
[4.3.6]: https://gitlab.com/ACP3/cms/compare/v4.3.5...v4.3.6
[4.3.5]: https://gitlab.com/ACP3/cms/compare/v4.3.4...v4.3.5
[4.3.4]: https://gitlab.com/ACP3/cms/compare/v4.3.3...v4.3.4
[4.3.3]: https://gitlab.com/ACP3/cms/compare/v4.3.2...v4.3.3
[4.3.2]: https://gitlab.com/ACP3/cms/compare/v4.3.1...v4.3.2
[4.3.1]: https://gitlab.com/ACP3/cms/compare/v4.3.0...v4.3.1
[4.3.0]: https://gitlab.com/ACP3/cms/compare/v4.2.0...v4.3.0
[4.2.0]: https://gitlab.com/ACP3/cms/compare/v4.1.30...v4.2.0
[4.1.30]: https://gitlab.com/ACP3/cms/compare/v4.1.29...v4.1.30
[4.1.29]: https://gitlab.com/ACP3/cms/compare/v4.1.28...v4.1.29
[4.1.28]: https://gitlab.com/ACP3/cms/compare/v4.1.27...v4.1.28
[4.1.27]: https://gitlab.com/ACP3/cms/compare/v4.1.22...v4.1.27
[4.1.22]: https://gitlab.com/ACP3/cms/compare/v4.1.21...v4.1.22
[4.1.21]: https://gitlab.com/ACP3/cms/compare/v4.1.20...v4.1.21
[4.1.20]: https://gitlab.com/ACP3/cms/compare/v4.1.19...v4.1.20
[4.1.19]: https://gitlab.com/ACP3/cms/compare/v4.1.18...v4.1.19
[4.1.18]: https://gitlab.com/ACP3/cms/compare/v4.1.17...v4.1.18
