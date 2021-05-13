# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [unreleased]

### Fixed

-   [Core] Fixed a possible PHP warning, if the authors section is missing within a composer.json file

## [5.18.0] - 2021-05-13

### Changed

-   [Core] The composer.json for the ACP3 components is mandatory now
-   [Core] Removed all the `module.xml` files from the modules

### Removed

-   [System] Removed the ability to enable or disable modules

### Fixed

-   [Core] Fixed an n+1 SQL-query problem when creating the module info cache

## [5.17.2] - 2021-05-08

### Changed

-   [Tests] Updated the PHPUnit configuration file

## [5.17.1] - 2021-05-08

### Fixed

-   [System] fix the ajax form complete callback

## [5.17.0] - 2021-05-07

### Added

-   [Core] Made it possible to extend the JavaScript SVG icons object with custom icons, too

### Changed

-   [ALL] Update PHP CS Fixer to version 3.0.0

### Fixed

-   [System] Fixed a Smarty error, when a WYSIWYG form-field was optional

## [5.16.0] - 2021-04-25

### Added

-   [System] Correctly set the `required`-HTML-Attribute on the wysiwyg form-group partial template

### Changed

-   [ALL] Dropped the support for PHP 7.2
-   [ALL] Replaced the usages of fontawesome webfont with its SVG-sprites
-   [Core] Updated Doctrine-DBAL to version 3.x
-   [Core] Update PHPUnit to version 9.5.
-   [Gallery] Do not allow to index the gallery picture details page, if the overlay has been enabled in the module
    settings

### Fixed

-   [Core, Comments, Guestbook] Fixed an erroneous return type declaration regarding the flood barrier, which could cause
    a type error under certain conditions
-   [Installer] Fixed the installers server-error page
-   [Installer] Fixed the available databases action to be more robust against errors

## [5.15.5] - 2021-03-05

### Fixed

-   [Core] Lock the `psr/container` package to version ~1.0.0, as there seems to be a BC break with version 1.1.0

## [5.15.4] - 2021-03-05

### Fixed

-   [Core] fix retrieving the theme specific translation overrides

## [5.15.3] - 2021-03-01

### Changed

-   [ALL] Explicitly defined the priority of the various exception listeners

## [5.15.2] - 2021-02-28

### Added

-   [Core] Vary the HTTP page cache by the `X-Requested-With` HTTP header too

## [5.15.1] - 2021-02-28

### Fixed

-   [Articles] Fixed the custom article layout retrieval, when editing an article

## [5.15.0] - 2021-02-27

### Added

-   [Core] Made it possible to let themes reside within Composer's vendor directory

### Changed

-   [ALL] Added support for Composer 2.x
-   [ALL] Increased PHPStan's level to level 5
-   [Core] Moved the `acp3:assets:clear` and `acp3:cache:clear` CLI-commands into the System module
-   [Core] Replaced the abandoned `patchwork/utf8` Composer package with Symfony's mbstring polyfill
-   [Default themes] Moved the `<!--JAVASCRIPTS-->` comment from the `<body>`-end into the `<head>`. This is no problem,
    as all the javascripts assets are deferred.
-   [System] Removed `html5shiv`. Internet Explorer versions <= 9 aren't supported anyway
-   [Core] Reworked the `acp3:components:paths` CLI command to include more information in the
    resulting `component-paths.json`. Be sure run `php bin/console.php acp3:components:paths` after the ACP3 version
    update before running the gulp tasks, as the new JSON-file version isn't compatible with the old one!

### Deprecations

-   [Core] Deprecated the `LESS` CSS preprocessor tool chain. To be removed with version 6.0.0. Use SCSS as a preprocessor
    instead.
-   [Core] Deprecated the following methods from the `ACP3\Core\Environment\ApplicationPath` class:
    -   `getClassesDir`
    -   `getModulesDir`
    -   `getDesignRootPathInternal`
    -   `setDesignRootPathInternal`
-   [Core] Deprecate the `ExtractFromPathTrait`

### Fixed

-   [System] Fixed saving the system settings

## [5.14.1] - 2021-02-06

### Fixed

-   [Captcha] Fixed initialising the reCaptcha

## [5.14.0] - 2021-02-05

### Added

-   [ALL] Added a simple mass action bar to the data grids
-   [Core] Reworked and optimized most of the gulp tasks
-   [Core] Added to the Smarty block functions `{tabset}` and `{tab}`
-   [Core] Introduced prettier

### Deprecated

-   [Core] Deprecated the `FrontendContext` class. It will be removed with ACP3 version 6.0
-   [Core] Deprecated the `AbstractFrontendAction` class. It will be removed with ACP3 version 6.0

### Changed

-   [ALL] Reduced the usage of jQuery and replaced it with "vanilla JS"
-   [ALL] Reworked all the event listeners to use the `EventSubscriberInterface`
-   [System] Replaced the `bootstrap-datimepicker`-library with flatpickr

### Fixed

-   [Core] Fixed the potential duplicate generation of static assets

## [5.13.2] - 2021-01-15

### Fixed

-   [Core] Applied some minor fixes to the gulp tasks

## [5.13.1] - 2021-01-11

### Changed

-   [Core] Changed stylelint to be a bit less strict

### Fixed

-   [Core] Fixed that stylelint was linting the `uploads` folder

## [5.13.0] - 2021-01-11

### Added

-   [Core] Bust the browser cache for the libraries when in development mode
-   [Core] Lint the stylesheets and the javascript files when running the gulp tasks
-   [System] Made it possible to specify the transfer method via a data attribute when using the ajax-form

### Changed

-   [Core] Generate the sourcemaps with gulp directly, instead of relying on the package `gulp-sourcemaps`
-   [Core] Changing the sort order of entries within a data grid will not trigger a full page reload anymore
-   [Core] Optimized the `Sort`-helper class
-   [Core] Optimized the `ACL`-class to remove some obsolete method calls
-   [Core] Refactor the gulp tasks
-   [Menus] Rework the menus' data grid
-   [Gallery] Removed some obsolete/redundant code from the `PictureModel`

### Fixed

-   [Core] fixed a possible infinite loop when running the `babel` gulp task in watch mode

## [5.12.2] - 2021-01-04

### Fixed

-   [System] Fixed a infinite recursion loop when clearing the page cache

## [5.12.1] - 2021-01-04

### Fixed

-   [Captcha] Fixed the captcha settings
-   [Installer] Fixed installing the sample data
-   [System] Fixed the system settings

## [5.12.0] - 2021-01-04

### Added

-   [ALL] Make use of symfony's DI container autowiring
-   [Core] Implemented different strategies for outputting the static assets (CSS and JavaScripts) in development mode and
    production mode
-   [Core] Made it possible to state the always enabled frontend libraries of a theme directly within the `info.xml`. Use
    the `<libraries><item>lib-name</item></libraries>` for this.
-   [Core] Added support for source maps

### Changed

-   [Core] Changed the `$moduleName` argument of the `LibraryEntity` to be mandatory. With version 6.0.0 this parameter
    with move to another position
-   [Core] Use the `CacheClearService` where possible
-   [Core] Replaced the `MigrationRegistrar` class with a "plain" symfony service locator
-   [Core] Replaced the `SampleDataRegistrar` class with a "plain" symfony service locator
-   [Core] Replaced the `AuthenticationRegistrar` class with a "plain" symfony service locator
-   [Core] Replaced the `WysiwygEditorRegistrar` class with a "plain" symfony service locator
-   [Core] Replaced the `ColumnTypeStrategyFactory` class with a "plain" symfony service locator
-   [Captcha] Print out the native captchas as inline images
-   [Captcha] Replaced the `CaptchaRegistrar` class with a "plain" symfony service locator
-   [Gallery] Reworked the gallery picture delete process to use events
-   [System] Exclude the installer theme to show up in the available themes
-   [TinyMCE] Removed the `content_css` feature
-   [Users] Clear the flash messages when logging in

### Deprecations

-   [Core] Deprecated the `ACP3\Core\Assets\Event\AddLibraryEvent`

### Fixed

-   [Core] Fixed the missing constructor argument of the `CacheClearService`

## [5.11.0] - 2021-01-02

### Added

-   [Core] Extended the `DisplayActionTrait` with the new method `renderTemplate()`
-   [Core] Extended the `TemplateEvent` class with the new methods `addContent` and `getContent`

### Changed

-   [Core] Got rid of the output buffering within the `{event}` Smarty function
-   [Core] Throw an `\InvalidArgumentException` if the `{event}` Smarty function gets called without a event name

### Deprecations

-   [Core] Deprecated the class `ACP3\Core\Controller\Event\CustomTemplateVariableEvent` and therefore the event itself
-   [Core] Deprecated the method `ACP3\Core\Controller\DisplayActionTrait::addCustomTemplateVarsBeforeOutput` and its
    implementations

### Fixed

-   [Core] Reverted the changes to the `CacheResponseTrait` and reworked it (breaking method signature change!), to
    hopefully fix some edge cases with the HTTP cache once and for all.
-   [System] Fixed the update check

## [5.10.2] - 2021-01-01

### Fixed

-   [Core] Fixed a regression introduced with version 5.10.1. We can't be thaaaat lazy about initializing the theme.

## [5.10.1] - 2021-01-01

### Changed

-   [Core] Reworked the `ACP3\Core\Assets` class, so that the theme initialization happens much later (and only when
    really necessary)

### Deprecations

-   [Core] Deprecated the method `ACP3\Core\Assets::getLibraries()`. Use `ACP3\Core\Assets\Libraries::getLibraries()`
    instead
-   [Core] Deprecated the method `ACP3\Core\Assets::getEnabledLibrariesAsString()`.
    Use `ACP3\Core\Assets\Libraries::getEnabledLibrariesAsString()` instead

### Fixed

-   [Core] Fixed a regression introduced with version 5.10.0 that made enabling the HTTP cache would result in incomplete
    asset delivery

## [5.10.0] - 2020-12-31

### Added

-   [Core] made it possible to register frontend libraries via the new DI container tag `acp3.assets.library`

### Changed

-   [Core] Moved adding the frontend assets from the `MoveToo*`-output filters into the `StaticAssetsListener`
-   [Core] Moved the `UserContextListener` into the users module
-   [Core] Integrate the HttpCache's EventDispatcher into the one from DI container
-   [System] The stylesheet of the `ajax-form` library is not deferrable anymore to fix a minor layout flash

### Deprecations

-   [Core] Deprecated the `ACP3\Core\View\Renderer\Smarty\Filters\AbstractMoveElementFilter`, of the `MoveTo*`-output
    filters within the core module are obsolete now

### Fixed

-   [Newsletter] Fixed subscribing the newsletter

## [5.9.2] - 2020-12-29

### Fixed

-   Fixed installing the Composer dependencies via the CI pipeline

## [5.9.1] - 2020-12-29

### Fixed

-   [Files] Fixed downloading local files

## [5.9.0] - 2020-12-28

### Added

-   [Core] Extended the `ControllerActionRequestEvent` with the ability to set a response
-   [Core] Implemented the `TerminableInterface` to allow running some longer processes without affecting the client's
    response times
-   [Core] Added the new interface `SortingAwareInterface` and trait `SortingAwareTrait` to extend the service models with
    the capability to change the order of DB results
-   [Core] The `AbstractNestedSetModel` classes implement the `SortingAwareInterface` by default now
-   [System] Allow to override the text-color of the "mandatory form-field star" using a SCSS variable
-   [System] Allow to override the margins of the pagination using a SCSS variable

### Changed

-   [Core] Increase the minimum supported PHP version to 7.2.5
-   [Core] Reworked the bootstrapping process, which allowed us to greatly simplify the front controllers
-   [Installer] Reworked the installer's requirements check, so that it automatically fetches the minimum required PHP
    version and to be installed PHP extensions from the `composer.json` files
-   [SEO] Move the regeneration of the XML-sitemaps to be handled by the `TerminateEvent`
-   [SEO] Combined 2 event listeners into a single one (which doesn't gets called that often) so slightly improve the
    performance
-   [System] Reworked the cache clear controller action page
-   [System] Removed the `<!-- JAVASCRIPT -->` placeholder from the content-only layout file

### Deprecations

-   [Core] Deprecated the method `ACP3\Core\Helpers\Alerts::errorBoxContent()`. Use `ACP3\Core\Helpers\Alerts::errorBox()`
    instead.

### Fixed

-   [Installer] Fixed the wrongly reported minimum PHP version of PHP 7.1.0 (now it is 7.2.5)
-   [System] Fixed possibly wrong colors of the `list-group-item` overrides

## [5.8.0] - 2020-12-26

### Changed

-   reworked the `ACP3\Core\Helpers\Alerts::confirmBox` and `ACP3\Core\Helpers\Alerts::confirmBoxPost` so that they return
    the already rendered template
    -   This allowed us to remove all the `*.delete.tpl`-templates
-   reworked the SchemaUpdater to be a little faster
-   optimized the bootstrapping process to that a sub request doesn't trigger a complete rebuild of the DI container
    anymore
-   optimized the inclusion of the reCaptcha assets, so that they are only loaded when really needed

### Fixed

-   The update check event listener was called way to often. This has been fixed now

## [5.7.0] - 2020-12-22

### Added

-   added the `acp3/module-menus` as a hard dependency to the `acp3/theme-default`

### Changed

-   slightly reworked and improved the layout system to fix some long-standing bugs
-   updated the NPM dependencies (especially the jQuery DataTables), to fix a possible security issue
-   dropped the RefreshListener from the HttpCache to fix errors with the Firefox browser
-   exclude non installable modules from the DB migrations
-   throw an error if a requested assets can't be resolved
-   removed all the leftover mentions of the articles module from the menus module
-   moved all DB queries from the various nested set operations into its repository class

### Fixed

-   Fixed saving ACL rules
-   Fixed the RSS/Atom feeds when there are no items to display
-   Fixed an incorrectly reported status when saving entities which are using the infrastructure of
    the `AbstractNestedSetModel`

## [5.6.0] - 2020-12-15

### Changed

-   renamed the `LibraryDto` to `LibraryEntity`. This also removes the possibility to enable a frontend library by default
    --> you have to call "enableLibraries" explicitly

## [5.5.1] - 2020-12-15

### Fixed

-   Fixed the error `Uncaught ReferenceError: jQuery is not defined` when the reCaptcha is active

## [5.5.0] - 2020-12-13

### Added

-   Made it possible to load some CSS files asynchronously
-   Continued to add SCSS variables to various modules

### Changed

-   `defer` loading the main javascript assets
-   The `ajax-form` library isn't enabled by default anymore
-   Refactored the poll creation form to use the ajax-form layout

### Fixed

-   Fixed that the social sharing module javascript assets where not being placed at the end of the `<body>`-tag

## [5.4.0] - 2020-12-12

### Added

-   Extended the SCSS files of the various modules with SCSS variables to make it easier to customize the ACP3 default
    appearance

### Fixed

-   Fixed the 404 error when trying to enter the social sharing module's settings page
-   Fixed the visuals of the rating stars of the social sharing module when ACP3's default theme is active

## [5.3.2] - 2020-12-12

### Fixed

-   Fixed a bug within the `AbstractModel`'s change detection

## [5.3.1] - 2020-12-12

### Fixed

-   Fixed the gallery list when there galleries without pictures

## [5.3.0] - 2020-12-05

### Changed

-   Updated the composer dependencies
-   Updated the NPM dependencies
-   Updated the PHPUnit tests according to changes introduced in PHPUnit 8.x
-   Replaced jQuery(document).ready() calls self-calling closures

### Fixed

-   Fixed the filemanager
-   fixed the missing fontawesome icons within the installer/update wizard

## [5.2.0] - 2020-11-29

### Changed

-   rework the AbstractModifier so that it allows more than one parameter

## [5.1.2] - 2020-11-27

### Fixed

-   fixed the missing sort icons within the data grids with the default theme
-   fixed some wrong icons

## [5.1.1] - 2020-11-22

### Fixed

-   version bump only

## [5.1.0] - 2020-11-22

### Changed

-   Extracted the cookie consent into its own module and reworked it
-   Replace the glyphicons with the font-awesome icons

## [5.0.1] - 2020-08-05

### Changed

-   optimized the module installation (fixed some n+1 SQL queries)
-   restricted the POST-only controllers actions to "normal" forms

### Fixed

-   fixed erroneous entries within the settings tables after a fresh installation

## [5.0.0] - 2020-07-19

### Breaking

-   removed all deprecated code

[unreleased]: https://gitlab.com/ACP3/cms/compare/v5.18.0...5.x
[5.18.0]: https://gitlab.com/ACP3/cms/compare/v5.17.2...v5.18.0
[5.17.2]: https://gitlab.com/ACP3/cms/compare/v5.17.1...v5.17.2
[5.17.1]: https://gitlab.com/ACP3/cms/compare/v5.17.0...v5.17.1
[5.17.0]: https://gitlab.com/ACP3/cms/compare/v5.16.0...v5.17.0
[5.16.0]: https://gitlab.com/ACP3/cms/compare/v5.15.5...v5.16.0
[5.15.5]: https://gitlab.com/ACP3/cms/compare/v5.15.4...v5.15.5
[5.15.4]: https://gitlab.com/ACP3/cms/compare/v5.15.3...v5.15.4
[5.15.3]: https://gitlab.com/ACP3/cms/compare/v5.15.2...v5.15.3
[5.15.2]: https://gitlab.com/ACP3/cms/compare/v5.15.1...v5.15.2
[5.15.1]: https://gitlab.com/ACP3/cms/compare/v5.15.0...v5.15.1
[5.15.0]: https://gitlab.com/ACP3/cms/compare/v5.14.1...v5.15.0
[5.14.1]: https://gitlab.com/ACP3/cms/compare/v5.14.0...v5.14.1
[5.14.0]: https://gitlab.com/ACP3/cms/compare/v5.13.2...v5.14.0
[5.13.2]: https://gitlab.com/ACP3/cms/compare/v5.13.1...v5.13.2
[5.13.1]: https://gitlab.com/ACP3/cms/compare/v5.13.0...v5.13.1
[5.13.0]: https://gitlab.com/ACP3/cms/compare/v5.12.2...v5.13.0
[5.12.2]: https://gitlab.com/ACP3/cms/compare/v5.12.1...v5.12.2
[5.12.1]: https://gitlab.com/ACP3/cms/compare/v5.12.0...v5.12.1
[5.12.0]: https://gitlab.com/ACP3/cms/compare/v5.11.0...v5.12.0
[5.11.0]: https://gitlab.com/ACP3/cms/compare/v5.10.2...v5.11.0
[5.10.2]: https://gitlab.com/ACP3/cms/compare/v5.10.1...v5.10.2
[5.10.1]: https://gitlab.com/ACP3/cms/compare/v5.10.0...v5.10.1
[5.10.0]: https://gitlab.com/ACP3/cms/compare/v5.9.2...v5.10.0
[5.9.2]: https://gitlab.com/ACP3/cms/compare/v5.9.1...v5.9.2
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
