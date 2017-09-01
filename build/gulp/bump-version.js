/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

const argv = require('yargs').argv;
const fs = require('fs');

module.exports = (gulp, plugins) => {
    "use strict";

    function getCurrentVersion() {
        const content = fs.readFileSync('./package.json');

        return JSON.parse(content).version;
    }

    function replaceAll(str, find, replace) {
        return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
    }

    function escapeRegExp(str) {
        return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
    }

    return () => {
        let from = getCurrentVersion();
        let version = from.split('.');

        if (argv.major) {
            version[0]++;
            version[1] = version[2] = 0;
        } else if (argv.minor) {
            version[1]++;
            version[2] = 0;
        } else if (argv.patch) {
            version[2]++;
        } else {
            plugins.util.log(plugins.util.colors.red('Error: Please specify the arguments "major", "minor" or "patch".'));
            return;
        }

        const to = version.join('.');

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
        )
            .pipe(plugins.change((content) => {
                return replaceAll(content, from, to);
            }))
            .pipe(gulp.dest('./'))
    }
};
