/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

module.exports = (gulp) => {
    'use strict';

    return () => {
        return gulp.watch(
            [
                'ACP3/Modules/*/*/Resources/Assets/less/**/*.less',
                'designs/*/**/Assets/less/*.less',
                'designs/*/**/Assets/less/**/*.less',
                'installation/design/Assets/less/*.less',
                'installation/Installer/Modules/*/Resources/Assets/less/style.less',
                'ACP3/Modules/*/*/Resources/Assets/scss/**/*.scss',
                'designs/*/**/Assets/scss/*.scss',
                'designs/*/**/Assets/scss/**/*.scss',
                'installation/design/Assets/scss/*.scss',
                'installation/Installer/Modules/*/Resources/Assets/scss/style.scss'
            ],
            {cwd: './'},
            gulp.series('less', 'scss')
        );
    };
};
