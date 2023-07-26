import componentPaths from "./helpers/component-paths.mjs";
import gulpPlumber from "gulp-plumber";
import gulpSharpResponsive from "gulp-sharp-responsive";

export default (gulp) => {
  return () => {
    return gulp
      .src(componentPaths.png, {
        base: ".",
        allowEmpty: true,
        since: gulp.lastRun("png"),
      })
      .pipe(gulpPlumber())
      .pipe(
        gulpSharpResponsive({
          formats: [
            {
              format: "png",
              pngOptions: {
                compressionLevel: 9,
                effort: 10,
              },
            },
          ],
        }),
      )
      .pipe(gulp.dest("./uploads/assets"));
  };
};
