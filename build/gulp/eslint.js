/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

module.exports = (gulp, plugins) => {
    'use strict';

    const componentPaths = require('./component-paths');
    const eslint = require('gulp-eslint');

    return () => {
        return gulp
            .src(
                componentPaths.js.concat(
                    [
                        './designs/*/*/Assets/js/{admin,frontend,partials,widget}/!(*.min).js',
                        './designs/*/Assets/js/!(*.min).js',
                    ]
                ),
                {base: './'}
            )
            .pipe(plugins.plumber())
            .pipe(eslint())
            .pipe(eslint.format());
    };
};
