import { globbySync } from "globby";
import componentPaths from "./build/gulp/helpers/component-paths.mjs";
import TerserPlugin from "terser-webpack-plugin";
import browserslist from "browserslist";
import { WebpackAssetsManifest } from "webpack-assets-manifest";
import MiniCssExtractPlugin from "mini-css-extract-plugin";
import RemoveEmptyScriptsPlugin from "webpack-remove-empty-scripts";
import CssMinimizerPlugin from "css-minimizer-webpack-plugin";
import * as lightningcss from "lightningcss";
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
      .replaceAll("scss", "css")
      .replace(/\.((m?js)|css)/, "")
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
    filename: "js/[name].[contenthash].js",
    sourceMapFilename: "js/[name].[contenthash].map",
    chunkFilename: "js/[id].[chunkhash].js",
  },
  resolve: {
    alias: componentPaths.pathAliases,
  },
  module: {
    rules: [
      {
        test: /\.(sa|sc|c)ss$/i,
        use: [
          MiniCssExtractPlugin.loader,
          "css-loader",
          "postcss-loader",
          {
            loader: "sass-loader",
            options: {
              sassOptions: { importers: [new sassEmbedded.NodePackageImporter()] },
            },
          },
        ],
      },
      {
        test: /\.m?js$/,
        exclude: /(node_modules)/,
        use: {
          loader: "swc-loader",
          options: {
            env: {
              coreJs: "3.43",
              mode: "usage",
              targets: browserslist(),
            },
          },
        },
      },
      {
        test: /\.(woff|woff2|eot|ttf|otf)$/i,
        type: "asset/resource",
        generator: {
          filename: "fonts/[hash][ext][query]",
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
      new CssMinimizerPlugin({
        minify: CssMinimizerPlugin.lightningCssMinify,
        minimizerOptions: {
          targets: lightningcss.browserslistToTargets(browserslist()),
        },
      }),
      new TerserPlugin({
        minify: TerserPlugin.swcMinify,
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
      filename: "css/[name].[contenthash].css",
      chunkFilename: "css/[id].[chunkhash].css",
      ignoreOrder: true,
    }),
  ],
};
