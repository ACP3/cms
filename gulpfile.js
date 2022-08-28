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

  gulp.task("clean", getTask("clean"));
  gulp.task("version-npm-libraries", getTask("version-npm-libraries"));
  gulp.task("copy-assets", gulp.series("clean", getTask("copy-assets")));
  gulp.task("scss", getTask("scss"));
  gulp.task("babel", getTask("webpack")); // @deprecated since ACP3 version 6.7.0, to be removed with version 7.0.0. Use `gulp webpack` instead.
  gulp.task("webpack", getTask("webpack"));
  gulp.task("eslint", getTask("eslint"));
  gulp.task("stylelint", getTask("stylelint"));
  gulp.task("lint", gulp.parallel("stylelint", "eslint"));
  gulp.task("bump-version", getTask("bump-version"));
  gulp.task("default", gulp.series("copy-assets", gulp.parallel("scss", "webpack", "lint")));

  gulp.task("watch", (done) => {
    process.env.GULP_MODE = "watch";

    return gulp.series("default", getTask("watch"))(done);
  });
})();
