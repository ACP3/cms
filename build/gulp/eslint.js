/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

module.exports = (gulp) => {
    'use strict';

    const componentPaths = require('./component-paths');
    const plumber = require('gulp-plumber');
    const eslint = require('gulp-eslint');

    return () => {
        return gulp
            .src(
                [
                    ...componentPaths.js.watch,
                    './designs/*/*/Assets/js/{admin,frontend,partials,widget,lib}/!(*.min).js',
                    './designs/*/Assets/js/!(*.min).js',
                ],
                {base: './'}
            )
            .pipe(plumber())
            .pipe(eslint())
            .pipe(eslint.format());
    };
};
