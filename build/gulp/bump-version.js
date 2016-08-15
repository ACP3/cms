/*
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

argv = require('yargs').argv;

module.exports = function (gulp, plugins) {
    "use strict";

    return function () {
        gulp.task('bump-version', function () {
            if (argv.from === undefined || argv.to === undefined) {
                plugins.util.log(plugins.util.colors.red('Error: Please specify the arguments "from" and "to".'));
                return;
            }

            function replaceAll(str, find, replace) {
                return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
            }

            function escapeRegExp(str) {
                return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
            }

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
                .pipe(plugins.change(function (content) {
                    return replaceAll(content, argv.from, argv.to);
                }))
                .pipe(gulp.dest('./'))
        });
    }
};
