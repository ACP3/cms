/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

module.exports = (gulp) => {
    'use strict';

    return (done) => {
        gulp.watch(
            [
                'ACP3/Modules/*/*/Resources/Assets/less/**/*.less',
                'designs/*/**/Assets/less/*.less',
                'designs/*/**/Assets/less/**/*.less',
                'installation/design/Assets/less/*.less',
                'installation/Installer/Modules/*/Resources/Assets/less/style.less',
            ],
            {cwd: './'},
            gulp.task('less')
        );
        gulp.watch(
            [
                'ACP3/Modules/*/*/Resources/Assets/scss/**/*.scss',
                'designs/*/**/Assets/scss/*.scss',
                'designs/*/**/Assets/scss/**/*.scss',
                'installation/design/Assets/scss/*.scss',
                'installation/Installer/Modules/*/Resources/Assets/scss/style.scss'
            ],
            {cwd: './'},
            gulp.task('scss')
        );
        gulp.watch(
            [
                './ACP3/Modules/*/*/Resources/Assets/js/{admin,frontend,partials}/*.js',
                './designs/*/*/Assets/js/{admin,frontend,partials}/*.js',
                './designs/*/Assets/js/*.js',
                './installation/design/Assets/js/*.js',
                './installation/Installer/Modules/*/Resources/Assets/js/*.js',
                // Exclude all already minified files
                '!./ACP3/Modules/*/*/Resources/Assets/js/{admin,frontend,partials}/*.min.js',
                '!./designs/*/Assets/js/*.min.js',
                '!./installation/design/Assets/js/*.min.js',
                '!./installation/Installer/Modules/*/Resources/Assets/js/*.min.js'
            ],
            {cwd: './'},
            gulp.task('babel')
        );

        done();
    };
};
