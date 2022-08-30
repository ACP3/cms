const componentPaths = require("./helpers/component-paths");

module.exports = (gulp) => {
  "use strict";

  return () => {
    return gulp.src(componentPaths.assets, { base: "." }).pipe(gulp.dest("./uploads/assets"));
  };
};
