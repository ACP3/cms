/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

module.exports = (gulp) => {
    'use strict';

    const autoprefixer = require('autoprefixer');
    const componentPaths = require('./component-paths');
    const plumber = require('gulp-plumber');
    const less = require('gulp-less');
    const postcss = require('gulp-postcss');
    const rename = require('gulp-rename');
    const dependents = require('gulp-dependents');
    const filter = require('gulp-filter');

    return () => {
        return gulp
            .src(
                [
                    ...componentPaths.less.watch,
                    './designs/*/**/Assets/less/**/*.less',
                ],
                {base: './', since: gulp.lastRun('less')},
            )
            .pipe(plumber())
            .pipe(dependents())
            .pipe(filter([
                '**',
                '!**/less/*/**/*.less',
            ]))
            .pipe(less())
            .pipe(postcss([autoprefixer()]))
            .pipe(rename((path) => {
                path.dirname = path.dirname.substring(0, path.dirname.length - 4) + 'css';
            }))
            .pipe(gulp.dest('./'));
    };
};
