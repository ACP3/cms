const globby = require("globby");
const componentPaths = require("./build/gulp/component-paths");
const TerserPlugin = require("terser-webpack-plugin");

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

module.exports = {
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
