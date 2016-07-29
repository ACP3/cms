/*
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

var gulp = require('gulp'),
    bowerBasePath = 'bower_components',
    systemBasePath = 'ACP3/Modules/ACP3/System/Resources/Assets';

gulp.task('cp-jquery', function() {
    gulp.src(bowerBasePath + '/jquery/dist/jquery.min.js')
        .pipe(gulp.dest(systemBasePath + '/js/libs'));
});

gulp.task('cp-bs', function () {
    var basePath = bowerBasePath + '/bootstrap/dist';

    gulp.src(basePath + '/js/bootstrap.min.js')
        .pipe(gulp.dest(systemBasePath + '/js/libs'));
    gulp.src(basePath + '/fonts/*')
        .pipe(gulp.dest(systemBasePath + '/fonts'));
    gulp.src(basePath + '/css/bootstrap.min.css')
        .pipe(gulp.dest(systemBasePath + '/css'));
});

gulp.task('cp-bootbox', function () {
    gulp.src(bowerBasePath + '/bootbox.js/bootbox.js')
        .pipe(gulp.dest(systemBasePath + '/js/libs'));
});

gulp.task('cp-moment', function () {
    gulp.src(bowerBasePath + '/moment/min/moment.min.js')
        .pipe(gulp.dest(systemBasePath + '/js/libs'));
});

gulp.task('cp-bs-datetime', function () {
    var basePath = bowerBasePath + '/eonasdan-bootstrap-datetimepicker/build';

    gulp.src(basePath + '/js/bootstrap-datetimepicker.min.js')
        .pipe(gulp.dest(systemBasePath + '/js/libs'));
    gulp.src(basePath + '/css/bootstrap-datetimepicker.css')
        .pipe(gulp.dest(systemBasePath + '/css'));
});

gulp.task('cp-dt', function() {
    var basePath = bowerBasePath + '/datatables.net';

    gulp.src(basePath + '/js/jquery.dataTables.min.js')
        .pipe(gulp.dest(systemBasePath + '/js/libs'));
    gulp.src(basePath + '-bs/css/dataTables.bootstrap.css')
        .pipe(gulp.dest(systemBasePath + '/css'));
});

gulp.task(
    'cp-libs',
    ['cp-jquery', 'cp-bs', 'cp-bootbox', 'cp-moment', 'cp-bs-datetime', 'cp-dt']
);
