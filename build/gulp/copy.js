/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

const libraryPaths = require('./libary-paths');

module.exports = function (gulp) {
    'use strict';

    return function () {
        for (const path of libraryPaths) {
            gulp.src(path.src)
                .pipe(gulp.dest(path.dest));
        }

        return 0;
    };
};
