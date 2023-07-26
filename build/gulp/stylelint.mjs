/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

import componentPaths from "./helpers/component-paths.mjs";
import gulpStylelint from "@ronilaukkarinen/gulp-stylelint";
import gulpPlumber from "gulp-plumber";

export default (gulp) => {
  return () => {
    return gulp
      .src(componentPaths.scss.watch, {
        base: ".",
        allowEmpty: true,
        since: gulp.lastRun("stylelint"),
      })
      .pipe(gulpPlumber())
      .pipe(
        gulpStylelint({
          reporters: [{ formatter: "string", console: true }],
        }),
      );
  };
};
