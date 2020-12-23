/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

const argv = require('yargs').argv;
const moment = require('moment');
const git = require('simple-git');
const semver = require('semver');
const yaml = require('js-yaml');
const fs = require('fs');

module.exports = (gulp, plugins) => {
    'use strict';

    function loadComponents() {
        const document = yaml.safeLoad(fs.readFileSync(__dirname + '/../../.gitsplit.yml', 'utf8'));

        const componentPathMap = new Map();
        const componentPaths = document.splits.map((split) => split.prefix);

        for (const componentPath of componentPaths) {
            const composerJson = require(__dirname + '/../../' + componentPath + '/composer.json');

            componentPathMap.set(componentPath, composerJson.name);
        }

        return componentPathMap;
    }

    /**
     * Returns the name of the current branch
     *
     * @returns {Promise<string>}
     */
    async function getNameOfCurrentBranch() {
        return (await git().raw(['branch', '--show-current'])).trim();
    }

    /**
     * Returns the latest tag of the current branch
     */
    async function getCurrentVersion() {
        const latestTagInBranch = await git().raw(['describe', '--abbrev=0']);

        if (latestTagInBranch.indexOf('v') === 0) {
            return latestTagInBranch.substring(1).trim();
        }

        return latestTagInBranch.trim();
    }

    /**
     * Returns an Array of the changed ACP3 components between the current version and the current branches HEAD
     *
     * @param {Map<string, string>} componentMap
     * @param {boolean} isMajorUpdate
     * @param {string} currentVersion
     * @returns {Promise<string[]>}
     */
    async function findChangedComponents(componentMap, isMajorUpdate, currentVersion) {
        // If we are dealing with a major version, return all modules
        if (isMajorUpdate) {
            return Array.from(componentMap.values());
        }

        const diffSummary = await git().diffSummary(['v' + currentVersion]);
        const changedComponents = new Set();

        for (const diffFile of diffSummary.files) {
            for (const [ componentPath, composerPackageName ] of componentMap) {
                if (diffFile.file.indexOf(componentPath) === 0 && !changedComponents.has(composerPackageName)) {
                    changedComponents.push(composerPackageName);
                }
            }
        }

        return Array.from(changedComponents);
    }

    /**
     * Bumps the version number of the various ACP3 components
     *
     * @param {string[]} changedComponents
     * @param {Map<string, string>} componentMap
     * @param {string} newVersion
     */
    function bumpVersions(changedComponents, componentMap, newVersion) {
        bumpCore(changedComponents, newVersion);
        bumpComponents(changedComponents, componentMap, newVersion);
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

    /**
     * Bumps the versions number of various files of the acp3/core component
     *
     * @param {string[]} changedComponents
     * @param {string} newVersion
     * @returns {*}
     */
    function bumpCore(changedComponents, newVersion) {
        gulp
            .src(
                ['./package.json', './package-lock.json'],
                {
                    base: './'
                }
            )
            .pipe(plugins.bump({version: newVersion}))
            .pipe(gulp.dest('./'));

        if (changedComponents.includes('acp3/core')) {
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

    /**
     *
     * @param {string} str
     * @param {string} find
     * @param {string} replace
     * @returns {string}
     */
    function replaceAll(str, find, replace) {
        return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
    }

    /**
     *
     * @param {string} str
     * @returns {string}
     */
    function escapeRegExp(str) {
        return str.replace(/([*?^=!:${}()|\/\\])/g, '\\$1');
    }

    /**
     * Bumps the version number within the composer.json files of the various ACP3 components.
     *
     * @param {string[]} changedComponents
     * @param {Map<string, string>} componentMap
     * @param {string} newVersion
     * @returns {*}
     */
    function bumpComponents(changedComponents, componentMap, newVersion) {
        const changedPaths = [];
        for (const [componentPath, composerPackageName] of componentMap) {
            if (!changedComponents.includes(composerPackageName)) {
                continue;
            }

            changedPaths.push(componentPath + '/composer.json');
        }

        return gulp.src(
            changedPaths,
            {
                base: './'
            }
        ).pipe(plugins.change((content) => {
            for (const packageName of changedComponents) {
                const search = '"' + packageName + '": "^[~0-9.]+"';
                const replace = '"' + packageName + '": "^' + newVersion + '"';

                content = replaceAll(content, search, replace);
            }

            return content;
        })).pipe(gulp.dest('./'));
    }

    /**
     * Finalizes the changelog for the to be released version
     *
     * @param {string} nameOfCurrentBranch
     * @param {string} currentVersion
     * @param {string} newVersion
     */
    function bumpChangelog(nameOfCurrentBranch, currentVersion, newVersion) {
        const changelogName = 'CHANGELOG-' + nameOfCurrentBranch + '.md';
        const changelogPath = __dirname + '/../../' + changelogName;

        if (!fs.existsSync(changelogPath)) {
            throw new Error(
                `Could not find the changelog with the name "${changelogName}". Please create it at first!`
            );
        }
        if (!fs.readFileSync(changelogPath).includes('## [Unreleased]')) {
            throw new Error(
                `Could not find an "## [Unreleased]" section within the changelog. Please create one at first!`
            );
        }

        gulp.src(
            [
                './' + changelogName,
            ]
        ).pipe(plugins.change((content) => {
            const currentDate = moment().format('YYYY-MM-DD');
            return content
                .replace('## [Unreleased]', `## [${newVersion}] - ${currentDate}`)
                .replace(
                    `[Unreleased]: https://gitlab.com/ACP3/cms/compare/v${currentVersion}...${nameOfCurrentBranch}`,
                    `[Unreleased]: https://gitlab.com/ACP3/cms/compare/v${newVersion}...${nameOfCurrentBranch}\n` +
                    `[${newVersion}]: https://gitlab.com/ACP3/cms/compare/v${currentVersion}...v${newVersion}`
                );
        })).pipe(gulp.dest('./'));
    }

    /**
     * Check, whether the version bumping can take place at all
     *
     * @param {string} nameOfCurrentBranch
     * @param {boolean} isMajorUpdate
     */
    function checkVersionBumpConstraints(nameOfCurrentBranch, isMajorUpdate) {
        if (!nameOfCurrentBranch.match(/^\d+\.x$/)) {
            throw new Error(
                `Can't bump the version outside a version branch. Please switch to one of them!`
            );
        } else if (isMajorUpdate) {
            const nextVersionBranch = Number(nameOfCurrentBranch.split('.')[0]) + 1;

            throw new Error(
                `Can't do a major version bump within branch "${nameOfCurrentBranch}". Please switch to branch "${nextVersionBranch}.x"!`
            );
        }
    }

    return async () => {
        try {
            const nameOfCurrentBranch = await getNameOfCurrentBranch();
            const currentVersion = await getCurrentVersion();
            const newVersion = getNewVersion(argv, currentVersion);
            const components = loadComponents();

            checkVersionBumpConstraints(nameOfCurrentBranch, argv.major);

            bumpChangelog(nameOfCurrentBranch, currentVersion, newVersion);

            bumpVersions(
                await findChangedComponents(components, argv.major, currentVersion),
                components,
                newVersion
            );
        } catch (e) {
            console.error(e.message);
        }
    };
};
