/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

import { existsSync } from "node:fs";
import { join, resolve } from "node:path";
import * as url from "url";

const __dirname = url.fileURLToPath(new URL(".", import.meta.url));

const file = join(__dirname, "../../../.component-paths.json");

if (!existsSync(file)) {
    console.error(
        `Could not find file ".component-paths.json" within the project's root directory.\nPlease run "php bin/console.php acp3:components:paths" first!`
    );
    process.exit(1);
}

import { createRequire } from "module";
const require = createRequire(import.meta.url);

/**
 * @type {Array<string, string[]>}
 */
const componentPathsJson = require(file);

const globPatternScss = "/Resources/Assets/scss/**/*.scss";
/** @type {string[]} */
const componentPathsScss = [
    ...Object.values(componentPathsJson.module)
        .concat(Object.values(componentPathsJson.installer))
        .map((module) => {
            return module + globPatternScss;
        }),
    ...Object.values(componentPathsJson.theme).map((theme) => {
        return theme + "/*" + globPatternScss;
    }),
];

const globPatternJsWatch = "/Resources/Assets/js/**/!(*.min).js";
/** @type {string[]} */
const componentPathsJsWatch = [
    ...Object.values(componentPathsJson.module)
        .concat(Object.values(componentPathsJson.installer))
        .map((module) => {
            return module + globPatternJsWatch;
        }),
    ...Object.values(componentPathsJson.theme).map((theme) => {
        return theme + "/*" + globPatternJsWatch;
    }),
];

const globPatternJsProcess = "/Resources/Assets/js/{admin,frontend,partials,widget}/!(*.min).js";
/** @type {string[]} */
const componentPathsJsProcess = [
    ...Object.values(componentPathsJson.module)
        .concat(Object.values(componentPathsJson.installer))
        .map((module) => {
            return module + globPatternJsProcess;
        }),
    ...Object.values(componentPathsJson.theme).map((theme) => {
        return theme + "/*" + globPatternJsProcess;
    }),
];

const globPatternWebp = "/Resources/Assets/img/**/*.{gif,png,jpg}";
/** @type {string[]} */
const componentPathsForWebpConversion = [
    ...Object.values(componentPathsJson.module)
        .concat(Object.values(componentPathsJson.installer))
        .map((module) => {
            return module + globPatternWebp;
        }),
    ...Object.values(componentPathsJson.theme).map((theme) => {
        return theme + "/*" + globPatternWebp;
    }),
];

const globPatternPng = "/Resources/Assets/img/**/*.png";
/** @type {string[]} */
const componentPathsForPngOptimization = [
    ...Object.values(componentPathsJson.module)
        .concat(Object.values(componentPathsJson.installer))
        .map((module) => {
            return module + globPatternPng;
        }),
    ...Object.values(componentPathsJson.theme).map((theme) => {
        return theme + "/*" + globPatternPng;
    }),
];

const globPatternAssets = "/Resources/Assets/**/*";
/** @type {string[]} */
const assetFolders = [
    ...Object.values(componentPathsJson.core)
        .concat(Object.values(componentPathsJson.module), Object.values(componentPathsJson.installer))
        .map((component) => {
            return component + globPatternAssets;
        }),
    ...Object.values(componentPathsJson.theme).map((theme) => {
        return theme + "/*" + globPatternAssets;
    }),
];

/**
 *
 * @param {string[]} paths
 * @returns {string[]}
 */
function filterComposerVendorComponents(paths) {
    return paths.filter((path) => !path.includes("./vendor/"));
}

let pathAliases = {};
Object.keys(componentPathsJson).forEach((componentType) => {
    for (const [componentName, componentPath] of Object.entries(componentPathsJson[componentType])) {
        pathAliases[componentName] = resolve(__dirname, "../../../", componentPath);
    }
});

export default {
    scss: {
        watch: filterComposerVendorComponents(componentPathsScss),
        all: componentPathsScss,
    },
    js: {
        watch: filterComposerVendorComponents(componentPathsJsWatch),
        all: componentPathsJsProcess, // this is only relevant for the webpack gulp task, as we want to copy all static assets into the "uploads/assets"-folder
    },
    webp: componentPathsForWebpConversion,
    png: componentPathsForPngOptimization,
    assets: assetFolders,
    pathAliases: pathAliases,
};
