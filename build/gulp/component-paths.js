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

module.exports = {
  less: {
    watch: modulePathsLessWatch.slice(),
    process: modulePathsLessProcess.slice(),
  },
  scss: modulePathsScss.slice(),
  js: {
    watch: modulePathsJsWatch.slice(),
    process: modulePathsJsProcess.slice(),
  },
};
