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
 * This function adds <script>-elements (e.g. after XHR requests) to the HTML documents <head>, so that the
 * javascript code can actually get executed.
 *
 * @param {HTMLElement} elementContainingScriptTags
 */
export function addScriptsToHead(elementContainingScriptTags) {
  const currentScriptsInHead = document.querySelectorAll("head script");
  const currentScriptSources = [];

  currentScriptsInHead.forEach((scriptTag) => {
    if (scriptTag.src) {
      currentScriptSources.push(scriptTag.src);
    }
  });

  for (const ajaxScriptElement of elementContainingScriptTags.getElementsByTagName("script")) {
    if (!ajaxScriptElement.src) {
      const scriptElement = document.createElement("script");
      scriptElement.innerHTML = ajaxScriptElement.innerHTML;
      document.head.appendChild(scriptElement);
    } else if (!currentScriptSources.includes(ajaxScriptElement.src)) {
      const scriptElement = document.createElement("script");
      scriptElement.src = ajaxScriptElement.src;
      document.head.appendChild(scriptElement);
    }
  }
}

/**
 * This function adds <link rel="stylesheet">-elements (e.g. after XHR requests) to the HTML documents <head>, so that the
 * stylesheet code can actually get executed.
 *
 * @param {HTMLElement} elementContainingLinkTags
 */
export function addStylesheetToHead(elementContainingLinkTags) {
  const currentStylesheetsInHead = document.querySelectorAll('head link[rel="stylesheet"]');
  const currentStylesheetSources = [];

  currentStylesheetsInHead.forEach((linkTag) => {
    if (linkTag.href) {
      currentStylesheetSources.push(linkTag.href);
    }
  });

  for (const ajaxLinkElement of elementContainingLinkTags.querySelectorAll('link[rel="stylesheet"]')) {
    if (!currentStylesheetSources.includes(ajaxLinkElement.href)) {
      const linkElement = document.createElement("link");
      linkElement.href = ajaxLinkElement.href;
      linkElement.rel = "stylesheet";
      linkElement.type = "text/css";
      document.head.appendChild(linkElement);
    }
  }
}
