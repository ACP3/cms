/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

module.exports = (gulp) => {
    'use strict';

    const autoprefixer = require('autoprefixer');
    const sass = require('gulp-sass');
    sass.compiler = require('node-sass');
    const componentPaths = require('./component-paths');
    const plumber = require('gulp-plumber');
    const postcss = require('gulp-postcss');
    const rename = require('gulp-rename');

    return () => {
        return gulp
            .src(
                componentPaths.scss.process.concat([
                    './designs/*/*/Assets/scss/*.scss',
                    './designs/*/Assets/scss/*.scss',
                ]),
                {base: './', allowEmpty: true}
            )
            .pipe(plumber())
            .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
            .pipe(postcss([autoprefixer()]))
            .pipe(rename((path) => {
                path.dirname = path.dirname.substring(0, path.dirname.length - 4) + 'css';
                path.basename += '.min';
            }))
            .pipe(gulp.dest('./'));
    };
};
