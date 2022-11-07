const componentPaths = require("./helpers/component-paths");
const plumber = require("gulp-plumber");
const sharpResponsive = require("gulp-sharp-responsive");

module.exports = (gulp) => {
  "use strict";

  return () => {
    return gulp
      .src(componentPaths.png, {
        base: ".",
        allowEmpty: true,
        since: gulp.lastRun("png"),
      })
      .pipe(plumber())
      .pipe(
        sharpResponsive({
          formats: [
            {
              format: "png",
              pngOptions: {
                compressionLevel: 9,
                effort: 10,
              },
            },
          ],
        })
      )
      .pipe(gulp.dest("./uploads/assets"));
  };
};
