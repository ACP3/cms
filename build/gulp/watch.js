/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

const componentPaths = require("./helpers/component-paths");

module.exports = (gulp) => {
  "use strict";

  return (done) => {
    gulp.watch(componentPaths.scss.watch, { cwd: "./" }, gulp.series("stylelint", "scss"));
    gulp.watch(componentPaths.js.watch, { cwd: "./" }, gulp.series("eslint"));

    done();
  };
};
