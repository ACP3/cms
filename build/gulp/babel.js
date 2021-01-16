/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

module.exports = (gulp) => {
    'use strict';

    const plumber = require('gulp-plumber');
    const rename = require('gulp-rename');
    const babel = require("gulp-babel");
    const componentPaths = require('./component-paths');

    return () => {
        return gulp
            .src(
                [
                    ...componentPaths.js,
                    './designs/*/*/Assets/js/{admin,frontend,partials,widget}/!(*.min).js',
                    './designs/*/Assets/js/!(*.min).js',
                ],
                {base: './', since: gulp.lastRun('babel'), sourcemaps: true}
            )
            .pipe(plumber())
            .pipe(babel())
            .pipe(rename((path) => {
                path.basename += '.min';
            }))
            .pipe(gulp.dest('./', { sourcemaps: '.' }));
    };
};
