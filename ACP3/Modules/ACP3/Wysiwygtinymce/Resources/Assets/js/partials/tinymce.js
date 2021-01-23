/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

/* global tinymce */
/* global tinyMCEFileBrowserCallback */

const initializeTinyMCEInstances = () => {
    jQuery('.wysiwyg-tinymce').each((index, element) => {
        const config = jQuery(element).data('wysiwygConfig');
        let fileManagerConfig = {};

        if (typeof tinyMCEFileBrowserCallback !== 'undefined') {
            fileManagerConfig = {
                file_browser_callback: tinyMCEFileBrowserCallback
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

(($) => {
    $(document).on('acp3.ajaxFrom.submit.before', () => {
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }
    });

    $(document).on('acp3.ajaxFrom.complete', () => {
        initializeTinyMCEInstances();
    });

    initializeTinyMCEInstances();
})(jQuery);
