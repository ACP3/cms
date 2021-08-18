/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

/* global tinymce */
/* global tinyMCEFileBrowserCallback */

const initializeTinyMCEInstances = () => {
  document.querySelectorAll(".wysiwyg-tinymce").forEach((element) => {
    const config = JSON.parse(element.dataset.wysiwygConfig);
    let fileManagerConfig = {};

    if (typeof tinyMCEFileBrowserCallback !== "undefined") {
      fileManagerConfig = {
        file_browser_callback: tinyMCEFileBrowserCallback,
      };
    }

    const finalConfig = {
      ...config,
      ...fileManagerConfig,
    };
    const existingInstance = tinymce.get(element.id);

    if (existingInstance) {
      existingInstance.remove();
    }

    tinymce.init(finalConfig);
  });
};

((document) => {
  document.addEventListener("acp3.ajaxFrom.submit.before", () => {
    if (typeof tinymce !== "undefined") {
      tinymce.triggerSave();
    }
  });

  document.addEventListener("acp3.ajaxFrom.complete", () => {
    initializeTinyMCEInstances();
  });

  initializeTinyMCEInstances();
})(document);
