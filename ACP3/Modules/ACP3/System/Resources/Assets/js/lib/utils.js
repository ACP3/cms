/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

export function mergeSettings(defaultSettings, constructorOptions, dataAttributeOptions) {
  const mergedSettings = { ...defaultSettings, ...constructorOptions };

  if (!dataAttributeOptions) {
    return mergedSettings;
  }

  for (let [key, value] of Object.entries(dataAttributeOptions)) {
    const keyStripped = lowerCaseFirstLetter(key.replace("ajaxForm", ""));

    if (keyStripped.length > 0 && typeof mergedSettings[keyStripped] !== "undefined") {
      try {
        mergedSettings[keyStripped] = JSON.parse(value);
      } catch {
        mergedSettings[keyStripped] = value;
      }
    }
  }

  return mergedSettings;
}

function lowerCaseFirstLetter(string) {
  return string.charAt(0).toLowerCase() + string.slice(1);
}

/**
 * This function add downloads javascript code (e.g. after XHR requests) to the HTML documents <head>, so that the
 * javascript code can actually get executed.
 *
 * @param {HTMLElement} elementContainingScriptTags
 */
export function addScriptsToHead(elementContainingScriptTags) {
  for (const ajaxScriptElement of elementContainingScriptTags.getElementsByTagName("script")) {
    const scriptElement = document.createElement("script");
    if (!ajaxScriptElement.src) {
      scriptElement.innerHTML = ajaxScriptElement.innerHTML;
    } else {
      scriptElement.src = ajaxScriptElement.src;
    }
    document.head.appendChild(scriptElement);
  }
}
