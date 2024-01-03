/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

import autoprefixer from "autoprefixer";
import * as sassEmbedded from "sass-embedded";
import gulpSass from "gulp-sass";
import componentPaths from "./helpers/component-paths.mjs";
import gulpPlumber from "gulp-plumber";
import gulpPostcss from "gulp-postcss";
import gulpRename from "gulp-rename";
import gulpDependents from "gulp-dependents";
import cssnano from "cssnano";

const sass = gulpSass(sassEmbedded);

export default (gulp) => {
  return () => {
    return gulp
      .src(componentPaths.scss.all, {
        base: ".",
        allowEmpty: true,
        since: gulp.lastRun("scss"),
      })
      .pipe(gulpPlumber())
      .pipe(gulpDependents())
      .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
      .pipe(gulpPostcss([autoprefixer(), cssnano()]))
      .pipe(
        gulpRename((path) => {
          path.dirname = path.dirname.substring(0, path.dirname.length - 4) + "css";
          path.extname = ".min.css";
        }),
      )
      .pipe(gulp.dest("./uploads/assets"));
  };
};
