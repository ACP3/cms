/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

(() => {
  "use strict";

  const gulp = require("gulp");

  function getTask(task) {
    return require("./build/gulp/" + task)(gulp);
  }

  gulp.task("copy", getTask("copy"));
  gulp.task("scss", getTask("scss"));
  gulp.task("babel", getTask("babel"));
  gulp.task("eslint", getTask("eslint"));
  gulp.task("stylelint-scss", getTask("stylelint-scss"));
  gulp.task("stylelint", gulp.parallel("stylelint-scss"));
  gulp.task("lint", gulp.parallel("stylelint", "eslint"));
  gulp.task("bump-version", getTask("bump-version"));
  gulp.task("default", gulp.parallel("scss", "babel", "lint"));

  gulp.task("watch", (done) => {
    process.env.GULP_MODE = "watch";

    return gulp.series(gulp.parallel("scss", "babel", "lint"), getTask("watch"))(done);
  });
})();
