module.exports = (gulp) => {
  "use strict";

  const path = require("path");
  const globby = require("globby");
  const componentPaths = require("./component-paths");
  const plumber = require("gulp-plumber");
  const webpack = require("webpack-stream");
  const TerserPlugin = require("terser-webpack-plugin");

  return () => {
    const entries = globby.sync([
      ...componentPaths.js.process,
      "./designs/*/*/Assets/js/{admin,frontend,partials,widget}/!(*.min).js",
      "./designs/*/*/Assets/js/!(*.min).js",
    ]);
    const entryPointMap = new Map();
    for (const entryPoint of entries) {
      entryPointMap.set(path.basename(entryPoint, path.extname(entryPoint)), entryPoint);
    }

    const webpackEntryConfig = {};
    entryPointMap.forEach((path, entryName) => {
      webpackEntryConfig[entryName] = {
        import: path,
        filename: path.substring(0, path.lastIndexOf("/")) + "/[name].min.js",
      };
    });

    return gulp
      .src(entries, {
        base: "./",
        allowEmpty: true,
        since: gulp.lastRun("webpack"),
      })
      .pipe(plumber())
      .pipe(
        webpack({
          devtool: "source-map",
          mode: "production",
          entry: webpackEntryConfig,
          module: {
            rules: [
              {
                test: /\.m?js$/,
                exclude: /(node_modules)/,
                use: {
                  loader: "babel-loader",
                  options: {
                    babelrc: true,
                  },
                },
              },
            ],
          },
          optimization: {
            minimize: true,
            minimizer: [
              new TerserPlugin({
                extractComments: false,
                terserOptions: {
                  format: {
                    comments: false,
                  },
                },
              }),
            ],
          },
        })
      )
      .pipe(gulp.dest("./"));
  };
};
