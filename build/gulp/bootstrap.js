/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

module.exports = function (gulp, plugins) {
    'use strict';

    const sass = require('gulp-sass');

    sass.compiler = require('node-sass');

    return function () {
        return gulp
            .src(
                [
                    './designs/*/System/Assets/scss/bootstrap.scss'
                ],
                {base: './'}
            )
            .pipe(plugins.plumber())
            .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
            .pipe(plugins.rename(function (path) {
                path.dirname = path.dirname.substring(0, path.dirname.length - 4) + 'css';
                path.basename += '.min';
            }))
            .pipe(gulp.dest('./'));
    };
};
