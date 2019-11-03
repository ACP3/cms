/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

module.exports = (gulp) => {
    'use strict';

    const componentPaths = require('./component-paths');

    return (done) => {
        gulp.watch(
            componentPaths.less.watch.concat([
                'designs/*/**/Assets/less/*.less',
                'designs/*/**/Assets/less/**/*.less',
            ]),
            {cwd: './'},
            gulp.task('less')
        );
        gulp.watch(
            componentPaths.scss.watch.concat([
                'designs/*/**/Assets/scss/*.scss',
                'designs/*/**/Assets/scss/**/*.scss',
            ]),
            {cwd: './'},
            gulp.task('scss')
        );
        gulp.watch(
            componentPaths.js.concat(
                [
                    './designs/**/Assets/js/**/*.js',
                    '!./designs/**/Assets/js/**/*.min.js',
                ]
            ),
            {cwd: './'},
            gulp.task('babel')
        );

        done();
    };
};
