import { globbySync } from "globby";
import componentPaths from "./build/gulp/helpers/component-paths.mjs";
import TerserPlugin from "terser-webpack-plugin";
import browserslist from "browserslist";
import { resolveToEsbuildTarget } from "esbuild-plugin-browserslist";

const entries = globbySync([...componentPaths.js.all, "./designs/*/*/Resources/Assets/js/!(*.min).js"]);
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

export default {
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
          loader: "esbuild-loader",
          options: {
            target: resolveToEsbuildTarget(browserslist()),
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
