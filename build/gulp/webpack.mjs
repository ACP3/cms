import { globbySync } from "globby";
import componentPaths from "./helpers/component-paths.mjs";
import gulpPlumber from "gulp-plumber";
import webpackStream from "webpack-stream";
import webpackConfig from "../../webpack.config.mjs";

export default (gulp) => {
  return () => {
    const isWatchMode = process.env.GULP_MODE === "watch";

    return gulp
      .src(globbySync([...componentPaths.js.all]), {
        base: ".",
        allowEmpty: true,
      })
      .pipe(gulpPlumber())
      .pipe(
        webpackStream({
          ...webpackConfig,
          watch: isWatchMode,
        }),
      )
      .pipe(gulp.dest("./uploads/assets"));
  };
};
