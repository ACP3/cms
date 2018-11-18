/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

module.exports = (gulp, plugins) => {
    'use strict';

    const sass = require('gulp-sass');

    sass.compiler = require('node-sass');

    return () => {
        return gulp
            .src(
                [
                    './ACP3/Modules/*/*/Resources/Assets/scss/style.scss',
                    './ACP3/Modules/*/*/Resources/Assets/scss/append.scss',
                    './designs/*/*/Assets/scss/*.scss',
                    './designs/*/Assets/scss/*.scss',
                    './installation/design/Assets/scss/*.scss',
                    './installation/Installer/Modules/*/Resources/Assets/scss/style.scss'
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
