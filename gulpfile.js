/*
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

var gulp = require('gulp'),
    bowerBasePath = 'bower_components',
    systemBasePath = 'ACP3/Modules/ACP3/System/Resources/Assets';

gulp.task('copy-jquery', function() {
    gulp.src(bowerBasePath + '/jquery/dist/jquery.min.js')
        .pipe(gulp.dest(systemBasePath + '/js/libs'));
});

gulp.task('copy-bs', function () {
    var bsBasePath = bowerBasePath + '/bootstrap/dist';

    gulp.src(bsBasePath + '/js/bootstrap.min.js')
        .pipe(gulp.dest(systemBasePath + '/js/libs'));

    gulp.src(bsBasePath + '/fonts/*')
        .pipe(gulp.dest(systemBasePath + '/fonts'));

    gulp.src(bsBasePath + '/css/bootstrap.min.css')
        .pipe(gulp.dest(systemBasePath + '/css'));
});

gulp.task('copy-bootbox', function () {
    gulp.src(bowerBasePath + '/bootbox.js/bootbox.js')
        .pipe(gulp.dest(systemBasePath + '/js/libs'));
});

gulp.task('copy', ['copy-jquery', 'copy-bs', 'copy-bootbox']);
