import componentPaths from "./helpers/component-paths.mjs";

export default (gulp) => {
    return () => {
        return gulp.src(componentPaths.assets, { base: "." }).pipe(gulp.dest("./uploads/assets"));
    };
};
