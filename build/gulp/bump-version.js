/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

const argv = require('yargs').argv;
const moment = require('moment');
const git = require('simple-git/promise');
const semver = require('semver');

module.exports = (gulp, plugins) => {
    'use strict';

    const modules = {
        'ACP3/Core': 'acp3/core',
        'designs/acp3': 'acp3/theme-default',
        'designs/acp3-installer': 'acp3/theme-installer',
        'installation': 'acp3/setup',
        'tests': 'acp3/test',
        'ACP3/Modules/ACP3/Acp': 'acp3/module-acp',
        'ACP3/Modules/ACP3/Articles': 'acp3/module-articles',
        'ACP3/Modules/ACP3/Articlesmenus': 'acp3/module-articles-menus',
        'ACP3/Modules/ACP3/Articlessearch': 'acp3/module-articles-search',
        'ACP3/Modules/ACP3/Articlesseo': 'acp3/module-articles-seo',
        'ACP3/Modules/ACP3/Articlesshare': 'acp3/module-articles-share',
        'ACP3/Modules/ACP3/Auditlog': 'acp3/module-audit-log',
        'ACP3/Modules/ACP3/Captcha': 'acp3/module-captcha',
        'ACP3/Modules/ACP3/Categories': 'acp3/module-categories',
        'ACP3/Modules/ACP3/Comments': 'acp3/module-comments',
        'ACP3/Modules/ACP3/Contact': 'acp3/module-contact',
        'ACP3/Modules/ACP3/Contactseo': 'acp3/module-contact-seo',
        'ACP3/Modules/ACP3/Cookieconsent': 'acp3/module-cookie-consent',
        'ACP3/Modules/ACP3/Emoticons': 'acp3/module-emoticons',
        'ACP3/Modules/ACP3/Errors': 'acp3/module-errors',
        'ACP3/Modules/ACP3/Feeds': 'acp3/module-feeds',
        'ACP3/Modules/ACP3/Filemanager': 'acp3/module-filemanager',
        'ACP3/Modules/ACP3/Files': 'acp3/module-files',
        'ACP3/Modules/ACP3/Filescomments': 'acp3/module-files-comments',
        'ACP3/Modules/ACP3/Filesfeed': 'acp3/module-files-feed',
        'ACP3/Modules/ACP3/Filessearch': 'acp3/module-files-search',
        'ACP3/Modules/ACP3/Filesseo': 'acp3/module-files-seo',
        'ACP3/Modules/ACP3/Filesshare': 'acp3/module-files-share',
        'ACP3/Modules/ACP3/Gallery': 'acp3/module-gallery',
        'ACP3/Modules/ACP3/Gallerycomments': 'acp3/module-gallery-comments',
        'ACP3/Modules/ACP3/Galleryseo': 'acp3/module-gallery-seo',
        'ACP3/Modules/ACP3/Galleryshare': 'acp3/module-gallery-share',
        'ACP3/Modules/ACP3/Guestbook': 'acp3/module-guestbook',
        'ACP3/Modules/ACP3/Guestbooknewsletter': 'acp3/module-guestbook-newsletter',
        'ACP3/Modules/ACP3/Installer': 'acp3/module-installer',
        'ACP3/Modules/ACP3/Menus': 'acp3/module-menus',
        'ACP3/Modules/ACP3/News': 'acp3/module-news',
        'ACP3/Modules/ACP3/Newscomments': 'acp3/module-news-comments',
        'ACP3/Modules/ACP3/Newsfeed': 'acp3/module-news-feed',
        'ACP3/Modules/ACP3/Newssearch': 'acp3/module-news-search',
        'ACP3/Modules/ACP3/Newsseo': 'acp3/module-news-seo',
        'ACP3/Modules/ACP3/Newsshare': 'acp3/module-news-share',
        'ACP3/Modules/ACP3/Newsletter': 'acp3/module-newsletter',
        'ACP3/Modules/ACP3/Permissions': 'acp3/module-permissions',
        'ACP3/Modules/ACP3/Polls': 'acp3/module-polls',
        'ACP3/Modules/ACP3/Search': 'acp3/module-search',
        'ACP3/Modules/ACP3/Seo': 'acp3/module-seo',
        'ACP3/Modules/ACP3/Share': 'acp3/module-social-sharing',
        'ACP3/Modules/ACP3/System': 'acp3/module-system',
        'ACP3/Modules/ACP3/Users': 'acp3/module-users',
        'ACP3/Modules/ACP3/Wysiwygckeditor': 'acp3/module-wysiwyg-ckeditor',
        'ACP3/Modules/ACP3/Wysiwygtinymce': 'acp3/module-wysiwyg-tinymce',
    };

    /**
     * Returns the latest tag of the current branch
     */
    function getCurrentVersion() {
        return git()
            .raw(['describe', '--abbrev=0'])
            .then((latestTagInBranch) => {
                if (latestTagInBranch.indexOf('v') === 0) {
                    return latestTagInBranch.substring(1).trim();
                }

                return latestTagInBranch.trim();
            });
    }

    /**
     *
     * @param {boolean} isMajorUpdate
     * @param {string} currentVersion
     * @returns {Promise<*>}
     */
    async function findChangedModules(isMajorUpdate, currentVersion) {
        // If we are dealing with a major version, return all modules
        if (isMajorUpdate) {
            return Object.values(modules);
        }

        const diffSummary = await git().diffSummary(['v' + currentVersion]);
        const changedModules = [];

        for (const diffFile of diffSummary.files) {
            for (const pathPrefix in modules) {
                if (!modules.hasOwnProperty(pathPrefix)) {
                    continue;
                }

                if (diffFile.file.indexOf(pathPrefix) === 0 && !changedModules.includes(modules[pathPrefix])) {
                    changedModules.push(modules[pathPrefix]);
                }
            }
        }

        return changedModules;
    }

    function bumpVersions(changedModules, newVersion) {
        bumpCore(changedModules, newVersion);
        bumpModules(changedModules, newVersion);
    }

    /**
     * Bumps a given semantic version number by the given console argument
     *
     * @param cliArgument
     * @param {string} currentVersion The to be bumped semver string
     * @returns {string} Returns the version bumped version string
     */
    function getNewVersion(cliArgument, currentVersion) {
        if (cliArgument.major) {
            return semver.inc(currentVersion, 'major');
        } else if (cliArgument.minor) {
            return semver.inc(currentVersion, 'minor');
        } else if (cliArgument.patch) {
            return semver.inc(currentVersion, 'patch');
        }

        throw new Error('Error: Please specify the arguments "major", "minor" or "patch"!');
    }

    function bumpCore(changedModules, newVersion) {
        gulp
            .src(
                ['./package.json', './package-lock.json'],
                {
                    base: './'
                }
            )
            .pipe(plugins.bump({version: newVersion}))
            .pipe(gulp.dest('./'));

        if (changedModules.includes('acp3/core')) {
            return gulp.src(
                './ACP3/Core/src/Application/BootstrapInterface.php',
                {
                    base: './'
                }
            ).pipe(plugins.change((content) => {
                const search = 'const VERSION = \'.+\'';
                const replace = 'const VERSION = \'' + newVersion + '\'';

                return replaceAll(content, search, replace);
            })).pipe(gulp.dest('./'));
        }
    }

    function replaceAll(str, find, replace) {
        return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
    }

    function escapeRegExp(str) {
        return str.replace(/([*?^=!:${}()|\/\\])/g, '\\$1');
    }

    function bumpModules(changedModules, newVersion) {
        const changedPaths = [];
        for (const pathPrefix in modules) {
            if (!modules.hasOwnProperty(pathPrefix) || !changedModules.includes(modules[pathPrefix])) {
                continue;
            }

            changedPaths.push(pathPrefix + '/composer.json');
        }

        return gulp.src(
            changedPaths,
            {
                base: './'
            }
        ).pipe(plugins.change((content) => {
            for (const packageName of changedModules) {
                const search = '"' + packageName + '": "^[~0-9.]+"';
                const replace = '"' + packageName + '": "^' + newVersion + '"';

                content = replaceAll(content, search, replace);
            }

            return content;
        })).pipe(gulp.dest('./'));
    }

    function bumpChangelog(currentVersion, newVersion) {
        gulp.src(
            [
                './CHANGELOG.md',
            ]
        ).pipe(plugins.change((content) => {
            const currentDate = moment().format('YYYY-MM-DD');
            return content
                .replace('## [Unreleased]', `## [${newVersion}] - ${currentDate}`)
                .replace(
                    `[Unreleased]: https://gitlab.com/ACP3/cms/compare/v${currentVersion}...4.x`,
                    `[Unreleased]: https://gitlab.com/ACP3/cms/compare/v${newVersion}...4.x\n` +
                    `[${newVersion}]: https://gitlab.com/ACP3/cms/compare/v${currentVersion}...v${newVersion}`
                );
        })).pipe(gulp.dest('./'));
    }

    return async () => {
        try {
            const currentVersion = await getCurrentVersion();
            const newVersion = getNewVersion(argv, currentVersion);

            bumpChangelog(currentVersion, newVersion);

            bumpVersions(
                await findChangedModules(argv.major, currentVersion),
                newVersion
            );
        } catch (e) {
            console.error(e.message);
        }
    };
};
