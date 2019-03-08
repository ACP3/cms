/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

module.exports = (gulp, plugins) => {
    'use strict';

    const autoprefixer = require('autoprefixer');

    return () => {
        return gulp
            .src(
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
            .pipe(plugins.plumber())
            .pipe(plugins.less())
            .pipe(plugins.postcss([autoprefixer()]))
            .pipe(plugins.rename((path) => {
                path.dirname = path.dirname.substring(0, path.dirname.length - 4) + 'css';
            }))
            .pipe(gulp.dest('./'));
    };
};
