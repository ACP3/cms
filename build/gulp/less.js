/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

module.exports = function (gulp, plugins) {
    "use strict";

    return function () {
        return gulp.src(
            [
                './ACP3/Modules/*/*/Resources/Assets/less/*.less',
                './designs/*/*/Assets/less/*.less',
                './designs/*/Assets/less/*.less',
                './installation/design/Assets/less/*.less',
                './installation/Installer/Modules/*/Resources/Assets/less/*.less'
            ],
            {base: './'}
        )
            .pipe(plugins.plumber())
            .pipe(plugins.less())
            .pipe(plugins.rename(function (path) {
                path.dirname = path.dirname.substring(0, path.dirname.length - 4) + 'css'
            }))
            .pipe(gulp.dest('./'));
    }
};
