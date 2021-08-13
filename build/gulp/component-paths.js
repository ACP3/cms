/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

/**
 * @type {Array<string, string[]>}
 */
const componentPaths = require("../../.component-paths.json");

const modulePathsScss = componentPaths.module.map((componentPath) => {
  return componentPath + "/Resources/Assets/scss/**/*.scss";
});
const modulePathsJsWatch = componentPaths.module.map((componentPath) => {
  return componentPath + "/Resources/Assets/js/{admin,frontend,partials,widget,lib}/!(*.min).js";
});
const modulePathsJsProcess = componentPaths.module.map((componentPath) => {
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
