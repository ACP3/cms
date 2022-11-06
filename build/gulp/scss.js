/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

const autoprefixer = require("autoprefixer");
const sass = require("gulp-sass")(require("sass"));
const componentPaths = require("./helpers/component-paths");
const plumber = require("gulp-plumber");
const postcss = require("gulp-postcss");
const rename = require("gulp-rename");
const dependents = require("gulp-dependents");
const cssnano = require("cssnano");

module.exports = (gulp) => {
  "use strict";

  return () => {
    return gulp
      .src(componentPaths.scss.all, {
        base: ".",
        allowEmpty: true,
        since: gulp.lastRun("scss"),
      })
      .pipe(plumber())
      .pipe(dependents())
      .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
      .pipe(postcss([autoprefixer(), cssnano()]))
      .pipe(
        rename((path) => {
          path.dirname = path.dirname.substring(0, path.dirname.length - 4) + "css";
          path.extname = ".min.css";
        })
      )
      .pipe(gulp.dest("./uploads/assets"));
  };
};
