/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

const fs = require("fs");
const path = require("path");

const file = path.join(__dirname, "../../../.component-paths.json");

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

const globPatternScss = "/Resources/Assets/scss/**/*.scss";
const componentPathsScss = [
  ...Object.values(componentPaths.module)
    .concat(Object.values(componentPaths.installer))
    .map((module) => {
      return module + globPatternScss;
    }),
  ...Object.values(componentPaths.theme).map((theme) => {
    return theme + "/*" + globPatternScss;
  }),
];

const globPatternJsWatch = "/Resources/Assets/js/**/!(*.min).js";
const componentPathsJsWatch = [
  ...Object.values(componentPaths.module)
    .concat(Object.values(componentPaths.installer))
    .map((module) => {
      return module + globPatternJsWatch;
    }),
  ...Object.values(componentPaths.theme).map((theme) => {
    return theme + "/*" + globPatternJsWatch;
  }),
];

const globPatternJsProcess = "/Resources/Assets/js/{admin,frontend,partials,widget}/!(*.min).js";
const componentPathsJsProcess = [
  ...Object.values(componentPaths.module)
    .concat(Object.values(componentPaths.installer))
    .map((module) => {
      return module + globPatternJsProcess;
    }),
  ...Object.values(componentPaths.theme).map((theme) => {
    return theme + "/*" + globPatternJsProcess;
  }),
];

const globPatternAssets = "/Resources/Assets/**/*";
const assetFolders = [
  ...Object.values(componentPaths.core)
    .concat(Object.values(componentPaths.module), Object.values(componentPaths.installer))
    .map((component) => {
      return component + globPatternAssets;
    }),
  ...Object.values(componentPaths.theme).map((theme) => {
    return theme + "/*" + globPatternAssets;
  }),
];

function filterComposerVendorComponents(paths) {
  return paths.filter((path) => !path.includes("./vendor/"));
}

let pathAliases = {};
Object.keys(componentPaths).forEach((componentType) => {
  for (const [componentName, componentPath] of Object.entries(componentPaths[componentType])) {
    pathAliases[componentName] = path.resolve(__dirname, "../../../", componentPath);
  }
});

module.exports = {
  scss: {
    watch: filterComposerVendorComponents(componentPathsScss),
    all: componentPathsScss,
  },
  js: {
    watch: filterComposerVendorComponents(componentPathsJsWatch),
    all: componentPathsJsProcess, // this is only relevant for the webpack gulp task, as we want to copy all static assets into the "uploads/assets"-folder
  },
  assets: assetFolders,
  pathAliases: pathAliases,
};
