/*
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

module.exports = function(gulp) {
    "use strict";

    gulp.task('watch', function () {
        // Watch all the .less files, then run the less task
        return gulp.watch(
            [
                './ACP3/Modules/*/*/Resources/Assets/less/**/*.less',
                './designs/*/**/Assets/less/*.less'
            ],
            ['less']
        );
    });
};
