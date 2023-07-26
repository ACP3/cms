import componentPaths from "./helpers/component-paths.mjs";
import gulpPlumber from "gulp-plumber";
import gulpSharpResponsive from "gulp-sharp-responsive";

export default (gulp) => {
  return () => {
    return gulp
      .src(componentPaths.webp, {
        base: ".",
        allowEmpty: true,
        since: gulp.lastRun("webp"),
      })
      .pipe(gulpPlumber())
      .pipe(
        gulpSharpResponsive({
          formats: [
            {
              format: "webp",
              webpOptions: {
                effort: 6,
                nearLossless: true,
              },
            },
          ],
        }),
      )
      .pipe(gulp.dest("./uploads/assets"));
  };
};
