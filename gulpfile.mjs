/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

import gulp from "gulp";
import { readdirSync } from "node:fs";
import { basename, extname } from "node:path";

async function getTask(taskName) {
  const exportedSymbols = await import("./build/gulp/" + taskName + ".mjs");
  return exportedSymbols.default(gulp);
}

const gulpTasks = readdirSync("./build/gulp/", { withFileTypes: true });
for (const file of gulpTasks.filter((file) => file.isFile())) {
  const taskName = basename(file.name, extname(file.name));
  gulp.task(taskName, await getTask(taskName));
}

gulp.task("copy-assets", gulp.series("clean", await getTask("copy-assets")));
gulp.task("babel", await getTask("webpack")); // @deprecated since ACP3 version 6.7.0, to be removed with version 7.0.0. Use `gulp webpack` instead.
gulp.task("lint", gulp.parallel("stylelint", "eslint"));
gulp.task(
  "default",
  gulp.series(gulp.parallel("copy-assets", "lint"), gulp.parallel("scss", "webp", "png", "webpack")),
);

gulp.task("watch", async (done) => {
  // This environment variable instructs the webpack gulp task to run in watch mode
  process.env.GULP_MODE = "watch";

  gulp.series(
    gulp.parallel("copy-assets", "lint"),
    gulp.parallel("scss", "webp", "png", "webpack", await getTask("watch")),
  )(done);
});
