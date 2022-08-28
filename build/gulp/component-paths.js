/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

const fs = require("fs");
const path = require("path");

const file = path.join(__dirname, "../../.component-paths.json");

if (!fs.existsSync(file)) {
  console.error(
    `Could not find file ".component-paths.json" within the project's root directory.\nPlease run "php bin/console.php acp3:components:paths" first!`
  );
  process.exit(1);
}

/**
 * @type {Array<string, string[]>}
 */
const componentPaths = require(file);

const modulePathsScss = [
  ...componentPaths.module.map((module) => {
    return module + "/Resources/Assets/scss/**/*.scss";
  }),
  ...componentPaths.theme.map((theme) => {
    return theme + "/*/Resources/Assets/scss/**/*.scss";
  }),
];
const modulePathsJsWatch = [
  ...componentPaths.module.map((module) => {
    return module + "/Resources/Assets/js/**/!(*.min).js";
  }),
  ...componentPaths.theme.map((theme) => {
    return theme + "/*/Resources/Assets/js/**/!(*.min).js";
  }),
];
const modulePathsJsProcess = [
  ...componentPaths.module.map((module) => {
    return module + "/Resources/Assets/js/{admin,frontend,partials,widget}/!(*.min).js";
  }),
  ...componentPaths.theme.map((theme) => {
    return theme + "/*/Resources/Assets/js/{admin,frontend,partials,widget}/!(*.min).js";
  }),
];
const assetFolders = [
  ...componentPaths.core.concat(componentPaths.module).map((component) => {
    return component + "/Resources/Assets/**/*";
  }),
  ...componentPaths.theme.map((theme) => {
    return theme + "/*/Resources/Assets/**/*";
  }),
];

function filterComposerVendorComponents(paths) {
  return paths.filter((path) => !path.includes("./vendor/"));
}

module.exports = {
  scss: {
    watch: filterComposerVendorComponents(modulePathsScss),
    all: modulePathsScss,
  },
  js: {
    watch: filterComposerVendorComponents(modulePathsJsWatch),
    process: filterComposerVendorComponents(modulePathsJsProcess),
    all: modulePathsJsProcess, // this is only relevant for the webpack gulp task, as we want to copy all static assets into the "uploads/assets"-folder
  },
  assets: assetFolders,
};
