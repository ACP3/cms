/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

import args from "yargs";
import git from "simple-git";
import semver from "semver";
import yaml from "js-yaml";
import { existsSync, readFileSync } from "node:fs";
import gulpBump from "gulp-bump";
import gulpChange from "gulp-change";
import * as url from "url";

const argv = args.argv;

const __dirname = url.fileURLToPath(new URL(".", import.meta.url));

export default function bumpVersion(gulp) {
    function loadComponents() {
        const document = yaml.load(readFileSync(__dirname + "/../../.gitsplit.yml", "utf8"));

        const componentPathMap = new Map();
        const componentPaths = document.splits.map((split) => split.prefix);

        for (const componentPath of componentPaths) {
            const composerJson = require(__dirname + "/../../" + componentPath + "/composer.json");

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
        return (await git().raw(["branch", "--show-current"])).trim();
    }

    /**
     * Returns the latest tag of the current branch
     */
    async function getCurrentVersion() {
        const latestTagInBranch = await git().raw(["describe", "--abbrev=0"]);

        if (latestTagInBranch.indexOf("v") === 0) {
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

        const diffSummary = await git().diffSummary(["v" + currentVersion]);
        const changedComponents = new Set();

        for (const diffFile of diffSummary.files) {
            for (const [componentPath, composerPackageName] of componentMap) {
                if (diffFile.file.indexOf(componentPath) === 0 && !changedComponents.has(composerPackageName)) {
                    changedComponents.add(composerPackageName);
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
        return Promise.all([
            // We need to bump the version number of the ACP3/core package everytime (even if there hasn't been any change),
            // as the update check is relying on this version.
            bumpCore(newVersion),
            bumpComponents(changedComponents, componentMap, newVersion),
        ]);
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
            return semver.inc(currentVersion, "major");
        }
        if (cliArgument.minor) {
            return semver.inc(currentVersion, "minor");
        }
        if (cliArgument.patch) {
            return semver.inc(currentVersion, "patch");
        }

        throw new Error('Error: Please specify the arguments "major", "minor" or "patch"!');
    }

    /**
     * Bumps the version numbers of various files of the acp3/core component
     *
     * @param {string} newVersion
     * @returns {*}
     */
    async function bumpCore(newVersion) {
        return Promise.all([
            new Promise((resolve, reject) => {
                gulp.src(["./package.json", "./package-lock.json"], {
                    base: "./",
                })
                    .pipe(gulpBump({ version: newVersion }))
                    .pipe(gulp.dest("./"))
                    .on("finish", resolve)
                    .on("error", reject);
            }),
            new Promise((resolve, reject) => {
                gulp.src("./ACP3/Core/src/Application/BootstrapInterface.php", {
                    base: "./",
                })
                    .pipe(
                        gulpChange((content) => {
                            const search = "const VERSION = '.+'";
                            const replace = "const VERSION = '" + newVersion + "'";

                            return replaceAll(content, search, replace);
                        })
                    )
                    .pipe(gulp.dest("./"))
                    .on("finish", resolve)
                    .on("error", reject);
            }),
        ]);
    }

    /**
     *
     * @param {string} str
     * @param {string} find
     * @param {string} replace
     * @returns {string}
     */
    function replaceAll(str, find, replace) {
        return str.replace(new RegExp(escapeRegExp(find), "g"), replace);
    }

    /**
     *
     * @param {string} str
     * @returns {string}
     */
    function escapeRegExp(str) {
        return str.replace(/([*?^=!:${}()|\/\\])/g, "\\$1");
    }

    /**
     * Bumps the version number within the composer.json files of the various ACP3 components.
     *
     * @param {string[]} changedComponents
     * @param {Map<string, string>} componentMap
     * @param {string} newVersion
     * @returns {*}
     */
    async function bumpComponents(changedComponents, componentMap, newVersion) {
        const changedPaths = [];
        for (const [componentPath, composerPackageName] of componentMap) {
            if (!changedComponents.includes(composerPackageName)) {
                continue;
            }

            changedPaths.push(componentPath + "/composer.json");
        }

        if (changedPaths.length === 0) {
            return Promise.resolve();
        }

        return new Promise((resolve, reject) => {
            gulp.src(changedPaths, {
                base: "./",
            })
                .pipe(
                    gulpChange((content) => {
                        for (const packageName of changedComponents) {
                            const search = '"' + packageName + '": "^[~0-9.]+"';
                            const replace = '"' + packageName + '": "^' + newVersion + '"';

                            content = replaceAll(content, search, replace);
                        }

                        return content;
                    })
                )
                .pipe(gulp.dest("./"))
                .on("finish", resolve)
                .on("error", reject);
        });
    }

    /**
     * Finalizes the changelog for the to be released version
     *
     * @param {string} nameOfCurrentBranch
     * @param {string} currentVersion
     * @param {string} newVersion
     */
    async function bumpChangelog(nameOfCurrentBranch, currentVersion, newVersion) {
        const changelogName = "CHANGELOG-" + nameOfCurrentBranch + ".md";
        const changelogPath = __dirname + "/../../" + changelogName;

        if (!existsSync(changelogPath)) {
            throw new Error(
                `Could not find the changelog with the name "${changelogName}". Please create it at first!`
            );
        }
        if (!readFileSync(changelogPath).includes("## [unreleased]")) {
            throw new Error(
                `Could not find an "## [unreleased]" section within the changelog. Please create one at first!`
            );
        }

        return new Promise((resolve, reject) => {
            gulp.src(["./" + changelogName])
                .pipe(
                    gulpChange((content) => {
                        return content
                            .replace("## [unreleased]", `## [${newVersion}] - ${getFormattedReleaseDate()}`)
                            .replace(
                                `[unreleased]: https://gitlab.com/ACP3/cms/compare/v${currentVersion}...${nameOfCurrentBranch}`,
                                `[unreleased]: https://gitlab.com/ACP3/cms/compare/v${newVersion}...${nameOfCurrentBranch}\n` +
                                    `[${newVersion}]: https://gitlab.com/ACP3/cms/compare/v${currentVersion}...v${newVersion}`
                            );
                    })
                )
                .pipe(gulp.dest("./"))
                .on("finish", resolve)
                .on("error", reject);
        });
    }

    function getFormattedReleaseDate() {
        const currentDate = new Date();
        return `${currentDate.getFullYear()}-${(currentDate.getMonth() + 1).toString().padStart(2, "0")}-${currentDate
            .getDate()
            .toString()
            .padStart(2, "0")}`;
    }

    /**
     * Check, whether the version bumping can take place at all
     *
     * @param {string} nameOfCurrentBranch
     * @param {string} newVersion
     * @param {boolean} isMajorUpdate
     */
    async function checkVersionBumpConstraints(nameOfCurrentBranch, newVersion, isMajorUpdate) {
        if (!nameOfCurrentBranch.match(/^\d+\.x$/)) {
            const versionBranch = Number(newVersion.split(".")[0]) + 1;

            throw new Error(
                `Can't bump the version outside a version branch. Please switch to branch "${versionBranch}.x"!`
            );
        }

        const newMajorVersion = Number(newVersion.split(".")[0]);
        const currentBranchPrefix = Number(nameOfCurrentBranch.split(".")[0]);
        if (isMajorUpdate && newMajorVersion !== currentBranchPrefix) {
            const nextVersionBranch = Number(nameOfCurrentBranch.split(".")[0]) + 1;

            throw new Error(
                `Can't do a major version bump within branch "${nameOfCurrentBranch}". Please switch to branch "${nextVersionBranch}.x"!`
            );
        }

        if ((await git().status()).modified.length) {
            throw new Error(
                `The working copy is not clean. Please commit all unsaved changes before running the bump-version task!`
            );
        }
    }

    /**
     * Commits the changes made by the bump-version task and creates the new tag.
     *
     * @param {string} newVersion
     */
    async function tagNewRelease(newVersion) {
        // Get the modified files so that we can commit these files
        const modifiedFiles = (await git().status()).modified;

        await git().commit(`bump the version to ${newVersion}`, modifiedFiles);
        await git().addAnnotatedTag(`v${newVersion}`, `v${newVersion}`);
    }

    /**
     * Commits the updated changelog.
     */
    async function commitExtendedChangelog() {
        // Get the modified files so that we can commit these files
        const modifiedFiles = (await git().status()).modified;

        await git().commit(`update the changelog`, modifiedFiles);
    }

    /**
     * Pushes the commits and the created tag
     */
    async function push() {
        await git().push();
        await git().pushTags();
    }

    /**
     *
     * @param {string} nameOfCurrentBranch
     * @param {string} newVersion
     * @returns {Promise<void>}
     */
    async function addChangelogEntryForNextRelease(nameOfCurrentBranch, newVersion) {
        const changelogName = "CHANGELOG-" + nameOfCurrentBranch + ".md";

        return new Promise((resolve, reject) => {
            gulp.src(["./" + changelogName])
                .pipe(
                    gulpChange((content) => {
                        const formattedDate = getFormattedReleaseDate();
                        return content.replace(
                            `## [${newVersion}] - ${formattedDate}`,
                            `## [unreleased]\n\ntba\n\n## [${newVersion}] - ${formattedDate}`
                        );
                    })
                )
                .pipe(gulp.dest("./"))
                .on("finish", resolve)
                .on("error", reject);
        });
    }

    return async (done) => {
        try {
            const nameOfCurrentBranch = await getNameOfCurrentBranch();
            const currentVersion = await getCurrentVersion();
            const newVersion = getNewVersion(argv, currentVersion);
            const components = loadComponents();

            await checkVersionBumpConstraints(nameOfCurrentBranch, newVersion, argv.major);

            await Promise.all([
                bumpChangelog(nameOfCurrentBranch, currentVersion, newVersion),
                bumpVersions(
                    await findChangedComponents(components, argv.major, currentVersion),
                    components,
                    newVersion
                ),
            ]);
            await tagNewRelease(newVersion);

            await addChangelogEntryForNextRelease(nameOfCurrentBranch, newVersion);
            await commitExtendedChangelog();
            await push();
        } catch (e) {
            console.error(e.message);
        } finally {
            done();
        }
    };
}
