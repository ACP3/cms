/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

module.exports = (gulp) => {
  "use strict";

  const componentPaths = require("./component-paths");

  return (done) => {
    gulp.watch(
      [...componentPaths.less.watch, "designs/*/**/Assets/less/**/*.less"],
      { cwd: "./" },
      gulp.parallel("less", "stylelint-less")
    );
    gulp.watch(
      [...componentPaths.scss, "designs/*/**/Assets/scss/**/*.scss"],
      { cwd: "./" },
      gulp.parallel("scss", "stylelint-scss")
    );
    gulp.watch(
      [...componentPaths.js.watch, "./designs/**/Assets/js/**/!(*.min).js"],
      { cwd: "./" },
      gulp.series("eslint")
    );

    done();
  };
};
