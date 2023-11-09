import { globbySync } from "globby";
import componentPaths from "./helpers/component-paths.mjs";
import gulpPlumber from "gulp-plumber";
import webpackStream from "webpack-stream";
import webpackConfig from "../../webpack.config.mjs";

export default (gulp) => {
  return () => {
    return gulp
      .src(globbySync([...componentPaths.js.all]), {
        base: ".",
        allowEmpty: true,
        since: gulp.lastRun("webpack"),
      })
      .pipe(gulpPlumber())
      .pipe(
        webpackStream({
          ...webpackConfig,
          watch: process.env.GULP_MODE === "watch",
        }),
      )
      .pipe(gulp.dest("./uploads/assets"));
  };
};
