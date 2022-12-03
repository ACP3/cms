/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

import componentPaths from "./helpers/component-paths.mjs";

export default (gulp) => {
    return (done) => {
        gulp.watch(componentPaths.scss.watch, { cwd: "./" }, gulp.series("stylelint", "scss"));
        gulp.watch(componentPaths.js.watch, { cwd: "./" }, gulp.series("eslint"));

        done();
    };
};
