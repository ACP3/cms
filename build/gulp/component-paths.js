/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

const fs = require("fs");
const path = require("path");

const file = path.join(__dirname, "../../.component-paths.json");

if (!fs.existsSync(file)) {
  console.error(
    `Could not find file "component-paths.json" into the project's root directory.\nPlease run "php bin/console.php acp3:components:paths" first!`
  );
  process.exit(1);
}

/**
 * @type {Array<string, string[]>}
 */
const componentPaths = require(file);

const modulePathsScss = componentPaths.module.concat(componentPaths.installer).map((componentPath) => {
  return componentPath + "/Resources/Assets/scss/**/*.scss";
});
const modulePathsJsWatch = componentPaths.module.concat(componentPaths.installer).map((componentPath) => {
  return componentPath + "/Resources/Assets/js/{admin,frontend,partials,widget,lib}/!(*.min).js";
});
const modulePathsJsProcess = componentPaths.module.concat(componentPaths.installer).map((componentPath) => {
  return componentPath + "/Resources/Assets/js/{admin,frontend,partials,widget}/!(*.min).js";
});

function filterComposerVendorComponents(paths) {
  return paths.filter((path) => !path.includes("./vendor/"));
}

module.exports = {
  scss: filterComposerVendorComponents(modulePathsScss),
  js: {
    watch: filterComposerVendorComponents(modulePathsJsWatch),
    process: filterComposerVendorComponents(modulePathsJsProcess),
  },
};
