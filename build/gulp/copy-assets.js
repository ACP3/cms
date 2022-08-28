module.exports = (gulp) => {
  "use strict";

  const componentPaths = require("./component-paths");

  return () => {
    return gulp.src(componentPaths.assets, { base: "." }).pipe(gulp.dest("./uploads/assets"));
  };
};
