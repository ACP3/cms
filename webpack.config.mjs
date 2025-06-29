import { globbySync } from "globby";
import componentPaths from "./build/gulp/helpers/component-paths.mjs";
import TerserPlugin from "terser-webpack-plugin";
import browserslist from "browserslist";
import { resolveToEsbuildTarget } from "esbuild-plugin-browserslist";
import { WebpackAssetsManifest } from "webpack-assets-manifest";
import MiniCssExtractPlugin from "mini-css-extract-plugin";
import RemoveEmptyScriptsPlugin from "webpack-remove-empty-scripts";
import * as sassEmbedded from "sass-embedded";

const entries = globbySync([
  ...componentPaths.scss.all,
  ...componentPaths.js.all,
  "./designs/*/*/Resources/Assets/js/!(*.min).js",
]);
const entryPointMap = new Map();
for (const entryPoint of entries) {
  entryPointMap.set(entryPoint, entryPoint);
}

const webpackEntryConfig = {};
entryPointMap.forEach((path, entryName) => {
  webpackEntryConfig[
    entryName
      .substring(2)
      .replace(/\.((m?js)|scss)/, "")
      .replace(/\/scss\//, "/css/")
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
    publicPath: "/uploads/assets/",
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
        // If you enable `experiments.css` or `experiments.futureDefaults`, please uncomment line below
        // type: "javascript/auto",
        test: /\.(sa|sc|c)ss$/i,
        use: [
          MiniCssExtractPlugin.loader,
          "css-loader",
          "postcss-loader",
          {
            loader: "sass-loader",
            options: {
              sassOptions: { style: "compressed", importers: [new sassEmbedded.NodePackageImporter()] },
            },
          },
        ],
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
    new RemoveEmptyScriptsPlugin(),
    new MiniCssExtractPlugin({
      filename: "[name].[contenthash].css",
      chunkFilename: "[id].[chunkhash].css",
    }),
  ],
};
