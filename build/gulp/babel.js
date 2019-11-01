/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

module.exports = (gulp, plugins) => {
    'use strict';

    const babel = require("gulp-babel");
    const componentPaths = require('./component-paths');

    return () => {
        return gulp
            .src(
                componentPaths.js.concat(
                    [
                        './designs/*/*/Assets/js/{admin,frontend,partials,widget}/*.js',
                        './designs/*/Assets/js/*.js',
                        './installation/design/Assets/js/*.js',
                        // Exclude already minified files
                        '!./designs/**/Assets/js/**/*.min.js',
                        '!./installation/design/Assets/js/*.min.js',
                    ]
                ),
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
