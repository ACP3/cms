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
            gulp.parallel('less', 'stylelint-less')
        );
        gulp.watch(
            componentPaths.scss.watch.concat([
                'designs/*/**/Assets/scss/*.scss',
                'designs/*/**/Assets/scss/**/*.scss',
            ]),
            {cwd: './'},
            gulp.parallel('scss', 'stylelint-less')
        );
        gulp.watch(
            componentPaths.js.concat(
                [
                    './designs/**/Assets/js/**/!(*.min).js',
                ]
            ),
            {cwd: './'},
            gulp.parallel('babel', 'eslint')
        );

        done();
    };
};
