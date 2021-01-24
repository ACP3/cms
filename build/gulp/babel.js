/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

module.exports = (gulp) => {
  "use strict";

  const rename = require("gulp-rename");
  const componentPaths = require("./component-paths");
  const browserify = require("browserify");
  const buffer = require("gulp-buffer");
  const sourcemaps = require("gulp-sourcemaps");
  const watchify = require("watchify");
  const globby = require("globby");
  const source = require("vinyl-source-stream");
  const eventStream = require("event-stream");
  const logger = require("gulplog");

  function getWatchifyHandler(bundler, fileName) {
    return function () {
      return bundler
        .bundle()
        .pipe(source(fileName))
        .pipe(buffer())
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(rename({ extname: ".min.js" }))
        .pipe(sourcemaps.write("./"))
        .pipe(gulp.dest("./"));
    };
  }

  return function (done) {
    let isFirstRun = true;

    const entries = globby.sync([
      ...componentPaths.js.process,
      "./designs/*/*/Assets/js/{admin,frontend,partials,widget}/!(*.min).js",
      "./designs/*/Assets/js/!(*.min).js",
    ]);

    const streams = entries.map((file) => {
      const opts = {
        ...watchify.args,
        ignoreMissing: true,
      };

      const bundler = process.env.GULP_MODE === "watch" ? watchify(browserify(file, opts)) : browserify(file, opts);
      bundler.plugin("tinyify").transform("babelify");

      const watchFn = getWatchifyHandler(bundler, file);

      bundler.on("update", () => {
        isFirstRun = false;

        return watchFn();
      });

      if (process.env.GULP_MODE === "watch") {
        bundler.on("log", () => {
          if (!isFirstRun) {
            logger.info(`Bundling file: %s`, file);
          }
        });
      }

      return watchFn();
    });

    return eventStream.merge(streams).on("end", done);
  };
};
