/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

/* global CKEDITOR */

const initializeCKEditorInstances = () => {
    jQuery('.wysiwyg-ckeditor').each((index, element) => {
        const config = jQuery(element).data('wysiwygConfig');

        if (typeof CKEDITOR.instances[element.id] !== 'undefined') {
            CKEDITOR.instances[element.id].destroy(true);
        }

        if (config) {
            CKEDITOR.replace(element.id, config);
        } else {
            CKEDITOR.replace(element.id);
        }
    });
};

jQuery(document).ready(($) => {
    $(document).on('acp3.ajaxFrom.submit.before', () => {
        if (typeof CKEDITOR === 'undefined') {
            return;
        }

        for (const instance in CKEDITOR.instances) {
            if (!Object.prototype.hasOwnProperty.call(CKEDITOR.instances, instance)) {
                return;
            }

            CKEDITOR.instances[instance].updateElement();
        }
    });

    $(document).on('acp3.ajaxFrom.complete', () => {
        initializeCKEditorInstances();
    });

    initializeCKEditorInstances();
});
