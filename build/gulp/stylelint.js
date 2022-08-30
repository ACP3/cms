/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

const componentPaths = require("./helpers/component-paths");
const gulpStylelint = require("@ronilaukkarinen/gulp-stylelint");
const plumber = require("gulp-plumber");

module.exports = (gulp) => {
  "use strict";

  return () => {
    return gulp
      .src(componentPaths.scss.watch, {
        base: ".",
        allowEmpty: true,
        since: gulp.lastRun("stylelint"),
      })
      .pipe(plumber())
      .pipe(
        gulpStylelint({
          reporters: [{ formatter: "string", console: true }],
        })
      );
  };
};
