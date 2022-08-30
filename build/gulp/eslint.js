/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

const componentPaths = require("./helpers/component-paths");
const plumber = require("gulp-plumber");
const eslint = require("gulp-eslint-new");

module.exports = (gulp) => {
  "use strict";

  return () => {
    return gulp
      .src(componentPaths.js.watch, { base: "./", since: gulp.lastRun("eslint") })
      .pipe(plumber())
      .pipe(eslint())
      .pipe(eslint.format());
  };
};
