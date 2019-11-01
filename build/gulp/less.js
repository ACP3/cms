/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

module.exports = (gulp, plugins) => {
    'use strict';

    const autoprefixer = require('autoprefixer');
    const componentPaths = require('./component-paths');

    console.log(componentPaths.less);

    return () => {
        return gulp
            .src(
                componentPaths.less.process.concat([
                    './designs/*/*/Assets/less/style.less',
                    './designs/*/*/Assets/less/append.less',
                    './designs/*/Assets/less/*.less',
                    './installation/design/Assets/less/*.less',
                ]),
                {base: './'}
            )
            .pipe(plugins.plumber())
            .pipe(plugins.less())
            .pipe(plugins.postcss([autoprefixer()]))
            .pipe(plugins.rename((path) => {
                path.dirname = path.dirname.substring(0, path.dirname.length - 4) + 'css';
            }))
            .pipe(gulp.dest('./'));
    };
};
