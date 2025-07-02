/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

/* global tinymce */

let windowHandle;
let tinymceFilePickerCallback;

window.SetUrl = (url) => {
  if (windowHandle) {
    windowHandle.close();
  }

  tinymceFilePickerCallback(url);
};

const initializeTinyMCEInstances = () => {
  document.querySelectorAll(".wysiwyg-tinymce").forEach((element) => {
    const config = JSON.parse(element.dataset.wysiwygConfig);
    let fileManagerConfig = {};

    if (config.fileBrowserBrowseUrl) {
      fileManagerConfig = {
        file_picker_callback: (callback) => {
          const width = screen.width * 0.7;
          const height = screen.height * 0.7;
          const left = (screen.width - width) / 2;
          const top = (screen.height - height) / 2;
          let windowOptions = "toolbar=no,status=no,resizable=yes,dependent=yes";
          windowOptions += ",width=" + width;
          windowOptions += ",height=" + height;
          windowOptions += ",left=" + left;
          windowOptions += ",top=" + top;

          tinymceFilePickerCallback = callback;
          windowHandle = window.open(config.fileBrowserBrowseUrl, "richfilemanager-popup", windowOptions);
        },
      };
    }

    const finalConfig = {
      license_key: "gpl",
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
