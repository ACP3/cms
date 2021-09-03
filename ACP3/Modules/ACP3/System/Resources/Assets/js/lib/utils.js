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
