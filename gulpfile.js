/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

const gulp = require("gulp");
const fs = require("fs");
const path = require("path");

(() => {
  "use strict";

  function getTask(task) {
    return require("./build/gulp/" + task)(gulp);
  }

  const gulpTasks = fs.readdirSync("./build/gulp/", { withFileTypes: true });

  gulpTasks
    .filter((file) => file.isFile())
    .forEach((file) => {
      const taskName = path.basename(file.name, path.extname(file.name));

      gulp.task(taskName, getTask(taskName));
    });

  gulp.task("copy-assets", gulp.series("clean", getTask("copy-assets")));
  gulp.task("babel", getTask("webpack")); // @deprecated since ACP3 version 6.7.0, to be removed with version 7.0.0. Use `gulp webpack` instead.
  gulp.task("lint", gulp.parallel("stylelint", "eslint"));
  gulp.task("default", gulp.series(gulp.parallel("copy-assets", "lint"), gulp.parallel("scss", "webpack")));

  gulp.task("watch", (done) => {
    // This environment variable instructs the webpack gulp task to run in watch mode
    process.env.GULP_MODE = "watch";

    gulp.series(gulp.parallel("copy-assets", "lint"), gulp.parallel("scss", "webpack", getTask("watch")))(done);
  });
})();
