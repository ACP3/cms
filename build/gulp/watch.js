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
                'ACP3/Modules/*/*/Resources/Assets/less/**/*.less',
                'designs/*/**/Assets/less/*.less',
                'installation/design/Assets/less/*.less',
                'installation/Installer/Modules/*/Resources/Assets/less/style.less'
            ],
            {cwd: './'},
            ['less']
        );
    };
};
