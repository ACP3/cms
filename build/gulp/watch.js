/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

const componentPaths = require("./component-paths");
module.exports = (gulp) => {
  "use strict";

  const componentPaths = require("./component-paths");

  return (done) => {
    gulp.watch(componentPaths.scss.watch, { cwd: "./" }, gulp.parallel("scss", "stylelint"));
    gulp.watch(componentPaths.js.watch, { cwd: "./" }, gulp.series("eslint"));

    done();
  };
};
