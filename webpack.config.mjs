import { globbySync } from "globby";
import componentPaths from "./build/gulp/helpers/component-paths.mjs";
import TerserPlugin from "terser-webpack-plugin";
import browserslist from "browserslist";
import { resolveToEsbuildTarget } from "esbuild-plugin-browserslist";
import { WebpackAssetsManifest } from "webpack-assets-manifest";

const entries = globbySync([...componentPaths.js.all, "./designs/*/*/Resources/Assets/js/!(*.min).js"]);
const entryPointMap = new Map();
for (const entryPoint of entries) {
  entryPointMap.set(entryPoint, entryPoint);
}

const webpackEntryConfig = {};
entryPointMap.forEach((path, entryName) => {
  webpackEntryConfig[
    entryName
      .substring(2)
      .replace(/\.m?js/, "")
      .replaceAll("/", "-")
      .toLocaleLowerCase()
  ] = {
    import: path,
  };
});

export default {
  devtool: "source-map",
  mode: "production",
  entry: webpackEntryConfig,
  output: {
    publicPath: "",
    filename: "[name].[contenthash].js",
    sourceMapFilename: "[name].[contenthash].map",
    chunkFilename: "[id].[chunkhash].js",
  },
  resolve: {
    alias: componentPaths.pathAliases,
  },
  module: {
    rules: [
      {
        test: /\.css$/i,
        use: ["style-loader", "css-loader"],
      },
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
    runtimeChunk: "single",
    splitChunks: {
      chunks: "all",
    },
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
  plugins: [
    new WebpackAssetsManifest({
      entrypoints: true,
      entrypointsKey: false,
    }),
  ],
};
