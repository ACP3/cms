/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

module.exports = (gulp, plugins) => {
    'use strict';

    const babel = require("gulp-babel");

    return () => {
        return gulp
            .src(
                [
                    './ACP3/Modules/*/*/Resources/Assets/js/{admin,frontend,partials}/*.js',
                    './designs/*/*/Assets/js/{admin,frontend,partials}/*.js',
                    './designs/*/Assets/js/*.js',
                    './installation/design/Assets/js/*.js',
                    './installation/Installer/Modules/*/Resources/Assets/js/*.js',
                    // Exclude all already minified files
                    '!./ACP3/Modules/*/*/Resources/Assets/js/{admin,frontend,partials}/*.min.js',
                    '!./designs/*/Assets/js/*.min.js',
                    '!./installation/design/Assets/js/*.min.js',
                    '!./installation/Installer/Modules/*/Resources/Assets/js/*.min.js'
                ],
                {base: './'}
            )
            .pipe(plugins.plumber())
            .pipe(babel())
            .pipe(plugins.rename((path) => {
                path.basename += '.min';
            }))
            .pipe(gulp.dest('./'));
    };
};
