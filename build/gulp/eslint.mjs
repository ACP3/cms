/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

import { componentPaths } from "./helpers/component-paths.mjs";
import gulpPlumber from "gulp-plumber";
import gulpEslint from "gulp-eslint-new";

export default function eslint(gulp) {
    return () => {
        return gulp
            .src(componentPaths.js.watch, { base: "./", since: gulp.lastRun("eslint") })
            .pipe(gulpPlumber())
            .pipe(gulpEslint())
            .pipe(gulpEslint.format());
    };
}
