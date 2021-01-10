/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

module.exports = (gulp) => {
    'use strict';

    const componentPaths = require('./component-paths');
    const gulpStylelint = require('gulp-stylelint');
    const plumber = require('gulp-plumber');

    return () => {
        return gulp
            .src(
                componentPaths.less.watch
                    .concat(componentPaths.scss.watch)
                    .concat([
                        './designs/*/*/Assets/{less,scss}/**/!(*.min).{scss,less}',
                        './designs/*/Assets/{less,scss}/!(*.min).{less,scss}',
                    ]),
                {base: './', allowEmpty: true}
            )
            .pipe(plumber())
            .pipe(gulpStylelint({
                reporters: [
                    {formatter: 'string', console: true}
                ]
            }));
    };
};
