/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

const argv = require('yargs').argv;

module.exports = (gulp, plugins) => {
    "use strict";

    /**
     * Reads the current ACP3 CMS version from the package.json
     */
    function getCurrentVersion() {
        return require('../../package.json').version;
    }

    function replaceAll(str, find, replace) {
        return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
    }

    function escapeRegExp(str) {
        return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
    }

    /**
     * Bumps a given semantic version number by the given console argument
     *
     * @param {string} version The to be bumped semver string
     * @returns {string} Returns the version bumped version string
     */
    function bumpVersion(version) {
        let versionArray = version.split('.');

        if (argv.major) {
            versionArray[0]++;
            versionArray[1] = versionArray[2] = 0;
        } else if (argv.minor) {
            versionArray[1]++;
            versionArray[2] = 0;
        } else if (argv.patch) {
            versionArray[2]++;
        } else {
            throw new Error('Error: Please specify the arguments "major", "minor" or "patch"!');
        }

        return versionArray.join('.');
    }

    return () => {
        try {
            const from = getCurrentVersion();
            const bumpedVersion = bumpVersion(from);

            return gulp.src(
                [
                    './ACP3/Core/composer.json',
                    './ACP3/Core/Application/BootstrapInterface.php',
                    './ACP3/Modules/ACP3/*/composer.json',
                    './installation/composer.json',
                    './package.json'
                ],
                {
                    base: './'
                }
            ).pipe(plugins.change((content) => {
                return replaceAll(content, from, bumpedVersion);
            })).pipe(gulp.dest('./'))
        } catch (e) {
            plugins.util.log(plugins.util.colors.red(e.message));
        }
    }
};
