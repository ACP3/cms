/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

module.exports = (gulp, plugins) => {
    'use strict';

    const autoprefixer = require('autoprefixer');
    const sass = require('gulp-sass');
    sass.compiler = require('node-sass');
    const componentPaths = require('./component-paths');

    return () => {
        return gulp
            .src(
                componentPaths.scss.process.concat([
                        './designs/*/*/Assets/scss/*.scss',
                        './designs/*/Assets/scss/*.scss',
                    ],
                ),
                {base: './'}
            )
            .pipe(plugins.plumber())
            .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
            .pipe(plugins.postcss([autoprefixer()]))
            .pipe(plugins.rename((path) => {
                path.dirname = path.dirname.substring(0, path.dirname.length - 4) + 'css';
                path.basename += '.min';
            }))
            .pipe(gulp.dest('./'));
    };
};
