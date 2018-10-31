/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

(function ($, window, document) {
    'use strict';

    let pluginName = 'formSubmit',
        defaults = {
            targetElement: '#content',
            loadingOverlay: true,
            loadingText: '',
            customFormData: null
        };

    function Plugin(element, options) {
        this.element = element;
        this.isFormValid = true;

        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    $.extend(Plugin.prototype, {
        init: function () {
            const that = this;

            this.mergeSettings();
            this.findSubmitButton();
            this.addLoadingLayer();
            this.element.noValidate = true;

            $(this.element).on('submit', function (e) {
                e.preventDefault();

                that.isFormValid = true;

                $(document).trigger('acp3.ajaxFrom.submit.before', [that]);

                if (that.isFormValid && that.preValidateForm(that.element)) {
                    that.processAjaxRequest();
                }
            }).on('click', function (e) {
                if ($(this).prop('tagName') === 'A') {
                    e.preventDefault();

                    that.processAjaxRequest();
                }
            }).on('change', function () {
                if (that.isFormValid === false) {
                    that.removeAllPreviousErrors(that.element);
                    that.checkFormElementsForErrors(that.element);
                }
            });
        },
        mergeSettings: function () {
            const data = $(this.element).data();
            for (let key in data) {
                if (data.hasOwnProperty(key)) {
                    const keyStripped = this.lowerCaseFirstLetter(key.replace('ajaxForm', ''));

                    if (keyStripped.length > 0 && typeof this.settings[keyStripped] !== 'undefined') {
                        this.settings[keyStripped] = data[key];
                    }
                }
            }
        },
        lowerCaseFirstLetter: function (string) {
            return string.charAt(0).toLowerCase() + string.slice(1);
        },
        findSubmitButton: function () {
            $(this.element).find(':submit').click(function () {
                $(':submit', $(this).closest('form')).removeAttr('data-clicked');
                $(this).attr('data-clicked', 'true');
            });
        },
        preValidateForm: function (form) {
            this.removeAllPreviousErrors(form);
            this.checkFormElementsForErrors(form);
            this.focusTabWithFirstErrorMessage();

            return this.isFormValid;
        },
        removeAllPreviousErrors: function (form) {
            $(form).find('.is-invalid, .invalid-feedback').remove();
        },
        checkFormElementsForErrors: function (form) {
            for (let i = 0; i < form.elements.length; i++) {
                const field = form.elements[i];

                if (field.nodeName !== 'INPUT' && field.nodeName !== 'TEXTAREA' && field.nodeName !== 'SELECT') {
                    continue;
                }

                if (!field.checkValidity()) {
                    this.addErrorMessageToFormField($(field), field.validationMessage);

                    this.isFormValid = false;
                }
            }
        },
        removeErrorMessageFromFormField: function ($elem) {
            $elem.closest('div').find('.invalid-feedback').remove();
        },
        addErrorMessageToFormField: function ($formField, errorMessage) {
            this.removeErrorMessageFromFormField($formField);

            $formField.addClass('is-invalid');

            const template = '<div class="invalid-feedback d-block"><i class="fas fa-exclamation-triangle"></i> ' + errorMessage + '</div>';

            $formField
                .closest('div:not(.input-group):not(.btn-group)')
                .append(template);
        },
        focusTabWithFirstErrorMessage: function () {
            if ($('.tabbable').length > 0) {
                let $elem = $('.tabbable .form-group:has(.invalid-feedback):first'),
                    tabId = $elem.closest('.tab-pane').prop('id');
                $('.tabbable .nav-tabs .nav-link[href="#' + tabId + '"]').tab('show');

                $elem.find(':input').focus();
            }
        },
        processAjaxRequest: function () {
            const $form = $(this.element),
                hasCustomData = !$.isEmptyObject(this.settings.customFormData);

            let hash,
                processData = true,
                data = this.settings.customFormData || {},
                $submitButton;

            if ($form.attr('method')) {
                $submitButton = $(':submit[data-clicked="true"]', $form);

                hash = $submitButton.data('hashChange');

                data = new FormData($form[0]);

                if ($submitButton.length) {
                    data.append($submitButton.attr('name'), 1);
                }

                if (hasCustomData) {
                    for (let key in this.settings.customFormData) {
                        if (this.settings.customFormData.hasOwnProperty(key)) {
                            data.append(key, this.settings.customFormData[key]);
                        }
                    }
                }

                processData = false;
            } else {
                hash = $form.data('hashChange');
            }

            $.ajax({
                url: $form.attr('action') || $form.attr('href'),
                type: $form.attr('method') ? $form.attr('method').toUpperCase() : 'GET',
                data: data,
                processData: processData,
                contentType: processData ? 'application/x-www-form-urlencoded; charset=UTF-8' : false,
                beforeSend: () => {
                    this.showLoadingLayer($submitButton);
                    this.disableSubmitButton($submitButton);
                }
            }).done((responseData) => {
                try {
                    let callback = $form.data('ajax-form-complete-callback');

                    if (typeof window[callback] === 'function') {
                        window[callback](responseData);
                    } else if (responseData.redirect_url) {
                        this.redirectToNewPage(hash, responseData);
                    } else {
                        this.scrollIntoView();
                        this.replaceContent(hash, responseData);
                        this.rebindHandlers(hash);

                        if (typeof hash !== 'undefined') {
                            window.location.hash = hash;
                        }
                    }
                } catch (err) {
                    console.error(err.message);
                }
            }).fail((jqXHR) => {
                if (jqXHR.status === 400) {
                    this.handleFormErrorMessages($form, jqXHR.responseText);
                    this.scrollIntoView();

                    $(document).trigger('acp3.ajaxFrom.submit.fail', [this]);
                } else if (jqXHR.responseText.length > 0) {
                    document.open();
                    document.write(jqXHR.responseText);
                    document.close();
                }
            }).always(() => {
                this.hideLoadingLayer();
                this.enableSubmitButton($submitButton);
            });
        },
        addLoadingLayer: function () {
            if (this.settings.loadingOverlay === false) {
                return;
            }

            let $loadingLayer = $('#loading-layer');
            if ($loadingLayer.length === 0) {
                let $body = $('body'),
                    loadingText = this.settings.loadingText || '',
                    html = '<div id="loading-layer" class="loading-layer"><h1><span class="fas fa-cog fa-spin"></span>' + loadingText + '</h1></div>';

                $(html).appendTo($body);
            }
        },
        showLoadingLayer: function () {
            $('#loading-layer').addClass('loading-layer__active');
        },
        disableSubmitButton: ($submitButton) => {
            if (typeof $submitButton !== 'undefined') {
                $submitButton.prop('disabled', true);
            }
        },
        enableSubmitButton: ($submitButton) => {
            if (typeof $submitButton !== 'undefined') {
                $submitButton.prop('disabled', false);
            }
        },
        redirectToNewPage: function (hash, responseData) {
            if (typeof hash !== 'undefined') {
                window.location.href = responseData.redirect_url + hash;
                window.location.reload();
            } else {
                window.location.href = responseData.redirect_url;
            }
        },
        /**
         * Scroll to the beginning of the content area, if the current viewport is near the bottom
         */
        scrollIntoView: function () {
            const offsetTop = $(this.settings.targetElement).offset().top;

            if ($(document).scrollTop() > offsetTop) {
                $('html, body').animate(
                    {
                        scrollTop: offsetTop
                    },
                    'fast'
                );
            }
        },
        replaceContent: function (hash, responseData) {
            if (hash && $(hash).length) {
                $(hash).html($(responseData).find(hash).html());
            } else {
                $(this.settings.targetElement).html(responseData);
            }
        },
        rebindHandlers: function (hash) {
            const $bindingTarget = (hash && $(hash).length) ? $(hash) : $(this.settings.targetElement);

            $bindingTarget.find('[data-ajax-form="true"]').formSubmit();

            this.findSubmitButton();
        },
        hideLoadingLayer: function () {
            $('#loading-layer').removeClass('loading-layer__active');
        },
        handleFormErrorMessages: function ($form, errorMessagesHtml) {
            let $errorBox = $('#error-box');
            const $modalBody = $form.find('.modal-body');

            $errorBox.remove();

            // Place the error messages inside the modal body for a better styling
            $errorBox = $(errorMessagesHtml);

            $errorBox
                .hide()
                .prependTo(($modalBody.length > 0 && $modalBody.is(':visible')) ? $modalBody : $form)
                .fadeIn();

            this.prettyPrintResponseErrorMessages($errorBox);
        },
        prettyPrintResponseErrorMessages: function ($errorBox) {
            const that = this;

            this.removeAllPreviousErrors(that.element);

            // highlight all input fields where the validation has failed
            $errorBox.find('li').each(function () {
                let $this = $(this),
                    errorClass = $this.data('error');
                if (errorClass.length > 0) {
                    let $elem = $('[id|="' + errorClass + '"]').filter(':not([id$="container"])');
                    if ($elem.length > 0) {
                        // Move the error message to the responsible input field(s)
                        // and remove the list item from the error box container
                        if ($elem.length === 1) {
                            that.addErrorMessageToFormField($elem, $this.html());
                            $this.remove();
                        }
                    }
                }
            });

            // if all list items have been removed, remove the error box container too
            if ($errorBox.find('li').length === 0) {
                $errorBox.remove();
            }

            this.focusTabWithFirstErrorMessage();
        }
    });

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
            }
        });
    };
})(jQuery, window, document);

jQuery(document).ready(function ($) {
    $('[data-ajax-form="true"]').formSubmit();

    $(document).on('draw.dt', function (e) {
        $(e.target).find('[data-ajax-form="true"]').formSubmit();
    });
});
