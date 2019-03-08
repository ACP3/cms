/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

(function () {
    'use strict';

    const gulp = require('gulp'),
        plugins = require('gulp-load-plugins')();

    function getTask(task) {
        return require('./build/gulp/' + task)(gulp, plugins);
    }

    gulp.task('copy', getTask('copy'));
    gulp.task('bump-version', getTask('bump-version'));
    gulp.task('scss', getTask('scss'));
    gulp.task('less', getTask('less'));
    gulp.task('watch', gulp.series('less', 'scss', getTask('watch')));

    gulp.task('default', gulp.series('watch'));
})();
