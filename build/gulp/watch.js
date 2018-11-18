/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

module.exports = function (gulp) {
    'use strict';

    return function () {
        // Watch all the .less files, then run the less task
        return gulp.watch(
            [
                'ACP3/Modules/*/*/Resources/Assets/scss/**/*.scss',
                'designs/*/**/Assets/scss/*.scss',
                'designs/*/**/Assets/scss/**/*.scss',
                'installation/design/Assets/scss/*.scss',
                'installation/Installer/Modules/*/Resources/Assets/scss/style.scss'
            ],
            {cwd: './'},
            gulp.series('scss', 'bootstrap', 'autoprefixer')
        );
    };
};
