/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

/**
 * @type {Array<string, string[]>}
 */
const componentPaths = require("../../.component-paths.json");

/**
 * @deprecated since version 5.15.0. To be removed with version 6.0.0. Use SCSS as a CSS preprocessor instead.
 */
const modulePathsLessWatch = componentPaths.module.map((componentPath) => {
  return componentPath + "/Resources/Assets/less/**/*.less";
});
/**
 * @deprecated since version 5.15.0. To be removed with version 6.0.0. Use SCSS as a CSS preprocessor instead.
 */
const modulePathsLessProcess = componentPaths.module.map((componentPath) => {
  return componentPath + "/Resources/Assets/less/*.less";
});
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
  /**
   * @deprecated since version 5.15.0. To be removed with version 6.0.0. Use SCSS as a CSS preprocessor instead.
   */
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
