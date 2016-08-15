/*
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

module.exports = function(gulp, $) {
    "use strict";

    gulp.task('less', function () {
        return gulp.src(
            [
                './ACP3/Modules/*/*/Resources/Assets/less/style.less',
                './ACP3/Modules/*/*/Resources/Assets/less/append.less',
                './designs/*/*/Assets/less/style.less',
                './designs/*/*/Assets/less/append.less',
                './designs/*/Assets/less/*.less',
                './installation/design/Assets/less/*.less',
                './installation/Installer/Modules/*/Resources/Assets/less/style.less'
            ],
            {base: './'}
        )
            .pipe($.plumber())
            .pipe($.less())
            .pipe($.rename(function (path) {
                path.dirname = path.dirname.substring(0, path.dirname.length - 4) + 'css'
            }))
            .pipe(gulp.dest('./'));
    });
};
