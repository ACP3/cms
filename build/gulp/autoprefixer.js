/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

module.exports = function (gulp, plugins) {
    'use strict';

    const autoprefixer = require('autoprefixer');

    return function () {
        return gulp
            .src(
                [
                    './ACP3/Modules/*/*/Resources/Assets/css/style.css',
                    './ACP3/Modules/*/*/Resources/Assets/css/append.css',
                    './designs/*/*/Assets/css/style.css',
                    './designs/*/*/Assets/css/append.css',
                    './designs/**/Assets/css/*.css',
                    './installation/design/Assets/css/*.css',
                    './installation/Installer/Modules/*/Resources/Assets/css/style.css',
                ],
                {base: './'}
            )
            .pipe(plugins.postcss([autoprefixer()]))
            .pipe(plugins.plumber())
            .pipe(gulp.dest('./'));
    };
};
