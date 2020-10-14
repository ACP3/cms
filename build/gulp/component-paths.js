/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

/**
 * @type {string[]}
 */
const componentPaths = require('../../.component-paths.json');

const modulePathsLessWatch = componentPaths.map((componentPath) => {
    return componentPath + '/Resources/Assets/less/**/*.less';
});
const modulePathsLessProcess = componentPaths.map((componentPath) => {
    return componentPath + '/Resources/Assets/less/{style,append}.less';
});
const modulePathsScssWatch = componentPaths.map((componentPath) => {
    return componentPath + '/Resources/Assets/scss/**/*.scss';
});
const modulePathsScssProcess = componentPaths.map((componentPath) => {
    return componentPath + '/Resources/Assets/scss/style.scss';
});
const modulePathsJs = componentPaths.map((componentPath) => {
    return componentPath + '/Resources/Assets/js/{admin,frontend,partials,widget}/*.js';
});
const modulePathsExcludeJs = componentPaths.map((componentPath) => {
    return '!' + componentPath + '/Resources/Assets/js/*/*.min.js';
});

module.exports = {
    less: {
        watch: modulePathsLessWatch.slice(),
        process: modulePathsLessProcess.slice(),
    },
    scss: {
        watch: modulePathsScssWatch.slice(),
        process: modulePathsScssProcess.slice()
    },
    js: modulePathsJs.concat(modulePathsExcludeJs).slice(),
};
