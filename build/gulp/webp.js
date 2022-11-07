const componentPaths = require("./helpers/component-paths");
const plumber = require("gulp-plumber");
const sharpResponsive = require("gulp-sharp-responsive");

module.exports = (gulp) => {
  "use strict";

  return () => {
    return gulp
      .src(componentPaths.webp, {
        base: ".",
        allowEmpty: true,
        since: gulp.lastRun("webp"),
      })
      .pipe(plumber())
      .pipe(
        sharpResponsive({
          formats: [
            {
              format: "webp",
              webpOptions: {
                effort: 6,
                nearLossless: true,
              },
            },
          ],
        })
      )
      .pipe(gulp.dest("./uploads/assets"));
  };
};
