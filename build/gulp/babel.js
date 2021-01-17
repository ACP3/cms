/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

module.exports = (gulp) => {
    'use strict';

    const plumber = require('gulp-plumber');
    const rename = require('gulp-rename');
    const componentPaths = require('./component-paths');
    const browserify = require('browserify');
    const buffer = require('gulp-buffer');
    const sourcemaps = require('gulp-sourcemaps');
    const tap = require('gulp-tap');

    return () => {
        return gulp
            .src(
                [
                    ...componentPaths.js.process,
                    './designs/*/*/Assets/js/{admin,frontend,partials,widget}/!(*.min).js',
                    './designs/*/Assets/js/!(*.min).js',
                ],
                {base: './', read: false, since: gulp.lastRun('babel')}
            )
            .pipe(plumber())
            .pipe(tap(function (file) {
                // replace file contents with browserify's bundle stream
                file.contents = browserify(file.path, {ignoreMissing: true})
                    .plugin('tinyify')
                    .transform('babelify')
                    .bundle();
            }))
            .pipe(buffer())
            .pipe(sourcemaps.init({loadMaps: true}))
            .pipe(rename((path) => {
                path.basename += '.min';
            }))
            .pipe(sourcemaps.write('./'))
            .pipe(gulp.dest('./'));
    };
};
