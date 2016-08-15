/*
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

(function () {
    "use strict";

    var gulp = require('gulp'),
        plugins = require('gulp-load-plugins')();

    function getTask(task) {
        return require('./build/gulp/' + task)(gulp, plugins);
    }

    gulp.task('copy', getTask('copy'));
    gulp.task('bump-version', getTask('bump-version'));
    gulp.task('less', getTask('less'));
    gulp.task('watch', getTask('watch'));

    gulp.task('default', ['watch']);
})();
