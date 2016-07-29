/*
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

var gulp = require('gulp');

gulp.task('copy-bs', function () {
    gulp.src('bower_components/bootstrap/dist/js/bootstrap.min.js')
        .pipe(gulp.dest('ACP3/Modules/ACP3/System/Resources/Assets/js/libs'));

    gulp.src('bower_components/bootstrap/dist/fonts/*')
        .pipe(gulp.dest('ACP3/Modules/ACP3/System/Resources/Assets/fonts'));

    gulp.src('bower_components/bootstrap/dist/css/bootstrap.min.css')
        .pipe(gulp.dest('ACP3/Modules/ACP3/System/Resources/Assets/css'));
});

gulp.task('copy', ['copy-bs']);
