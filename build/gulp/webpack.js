module.exports = (gulp) => {
  "use strict";

  const globby = require("globby");
  const componentPaths = require("./component-paths");
  const plumber = require("gulp-plumber");
  const webpack = require("webpack-stream");

  return () => {
    return gulp
      .src(globby.sync([...componentPaths.js.all, "./designs/*/*/Resources/Assets/js/!(*.min).js"]), {
        base: ".",
        allowEmpty: true,
        since: gulp.lastRun("webpack"),
      })
      .pipe(plumber())
      .pipe(webpack(require("../../webpack.config.js")))
      .pipe(gulp.dest("./uploads/assets"));
  };
};
