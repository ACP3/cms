module.exports = (gulp) => {
  "use strict";

  const globby = require("globby");
  const componentPaths = require("./component-paths");
  const plumber = require("gulp-plumber");
  const webpack = require("webpack-stream");
  const TerserPlugin = require("terser-webpack-plugin");

  return () => {
    const entries = globby.sync([...componentPaths.js.all, "./designs/*/*/Resources/Assets/js/!(*.min).js"]);
    const entryPointMap = new Map();
    for (const entryPoint of entries) {
      entryPointMap.set(entryPoint, entryPoint);
    }

    const webpackEntryConfig = {};
    entryPointMap.forEach((path, entryName) => {
      webpackEntryConfig[entryName] = {
        import: path,
        filename: (pathData) => pathData.runtime.replace(".js", ".min.js"),
      };
    });

    const webpackConfig = {
      watch: process.env.GULP_MODE === "watch",
      devtool: "source-map",
      mode: "production",
      entry: webpackEntryConfig,
      output: {
        publicPath: "",
      },
      resolve: {
        alias: componentPaths.pathAliases,
      },
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
    };

    return gulp
      .src(entries, {
        base: ".",
        allowEmpty: true,
        since: gulp.lastRun("webpack"),
      })
      .pipe(plumber())
      .pipe(webpack(webpackConfig))
      .pipe(gulp.dest("./uploads/assets"));
  };
};
