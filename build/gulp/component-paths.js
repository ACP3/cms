/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

/**
 * @type {string[]}
 */
const componentPaths = require("../../.component-paths.json");

const modulePathsLessWatch = componentPaths.map((componentPath) => {
  return componentPath + "/Resources/Assets/less/**/*.less";
});
const modulePathsLessProcess = componentPaths.map((componentPath) => {
  return componentPath + "/Resources/Assets/less/*.less";
});
const modulePathsScss = componentPaths.map((componentPath) => {
  return componentPath + "/Resources/Assets/scss/**/*.scss";
});
const modulePathsJsWatch = componentPaths.map((componentPath) => {
  return componentPath + "/Resources/Assets/js/{admin,frontend,partials,widget,lib}/!(*.min).js";
});
const modulePathsJsProcess = componentPaths.map((componentPath) => {
  return componentPath + "/Resources/Assets/js/{admin,frontend,partials,widget}/!(*.min).js";
});

function filterComposerVendorComponents(paths) {
  return paths.filter((path) => !path.includes("/vendor/"));
}

module.exports = {
  less: {
    watch: filterComposerVendorComponents(modulePathsLessWatch),
    process: filterComposerVendorComponents(modulePathsLessProcess),
  },
  scss: filterComposerVendorComponents(modulePathsScss),
  js: {
    watch: filterComposerVendorComponents(modulePathsJsWatch),
    process: filterComposerVendorComponents(modulePathsJsProcess),
  },
};
