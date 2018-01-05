/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

(function ($, window, document) {
    'use strict';

    const pluginName = 'formSubmit',
        defaults = {
            targetElement: '#content',
            customFormData: null,
            loadingLayerActiveClass: 'loading-layer_active'
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
            this.findSubmitButton();
            this.element.noValidate = true;

            $(this.element)
                .off()
                .on('submit', (e) => {
                    e.preventDefault();

                    this.isFormValid = true;

                    $(document).trigger('acp3.ajaxFrom.submit.before', [this]);

                    if (this.isFormValid && this.preValidateForm()) {
                        this.processAjaxRequest();
                    }
                })
                .on('click', (e) => {
                    if ($(this.element).prop('tagName') === 'A') {
                        e.preventDefault();

                        this.processAjaxRequest();
                    }
                })
                .on('change', () => {
                    if (this.isFormValid === false) {
                        this.removeAllPreviousErrors();
                        this.checkFormElementsForErrors(this.element);
                    }
                });
        },
        findSubmitButton: function () {
            $(this.element).find(':submit').click(function () {
                $(':submit', $(this).closest('form')).removeAttr('data-clicked');
                $(this).attr('data-clicked', 'true');
            });
        },
        preValidateForm: function () {
            this.removeAllPreviousErrors();
            this.checkFormElementsForErrors(this.element);
            this.focusTabWithFirstErrorMessage();

            return this.isFormValid;
        },
        removeAllPreviousErrors: function () {
            $('form .form-group.has-error')
                .removeClass('has-error')
                .find('.validation-failed').remove();
        },
        checkFormElementsForErrors: function (form) {
            for (const field of form.elements) {
                if (field.nodeName !== 'INPUT' && field.nodeName !== 'TEXTAREA' && field.nodeName !== 'SELECT') {
                    continue;
                }

                if (!field.checkValidity()) {
                    this.addErrorDecorationToFormGroup($(field));
                    this.addErrorMessageToFormField($(field), field.validationMessage);

                    this.isFormValid = false;
                }
            }
        },
        addErrorDecorationToFormGroup: function ($elem) {
            $elem.closest('.form-group').addClass('has-error');
        },
        removeErrorMessageFromFormField: function ($elem) {
            $elem.closest('div').find('.validation-failed').remove();
        },
        addErrorMessageToFormField: function ($formField, errorMessage) {
            this.removeErrorMessageFromFormField($formField);

            $formField
                .closest('div:not(.input-group):not(.btn-group)')
                .append(
                    '<small class="help-block validation-failed"><i class="fa fa-exclamation-triangle"></i> ' + errorMessage + '</small>'
                );
        },
        focusTabWithFirstErrorMessage: function () {
            if ($('.tabbable').length > 0) {
                const $elem = $('.tabbable .form-group.has-error:first'),
                    tabId = $elem.closest('.tab-pane').prop('id');
                $('.tabbable .nav-tabs a[href="#' + tabId + '"]').tab('show');

                $elem.find(':input').focus();
            }
        },
        processAjaxRequest: function () {
            let hash,
                $form = $(this.element),
                $submitButton,
                hasCustomData = !$.isEmptyObject(this.settings.customFormData),
                processData = true,
                data = this.settings.customFormData || {};

            if ($form.attr('method')) {
                $submitButton = $(':submit[data-clicked="true"]', $form);

                hash = $submitButton.data('hashChange');

                data = new FormData($form[0]);

                if ($submitButton.length) {
                    data.append($submitButton.attr('name'), 1);
                }

                if (hasCustomData) {
                    for (const key in this.settings.customFormData) {
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
                }
            }).done((responseData) => {
                try {
                    const callback = $form.data('ajax-form-complete-callback');

                    if (typeof window[callback] === 'function') {
                        window[callback](responseData);
                    } else if (responseData.redirect_url) {
                        this.redirectToNewPage(hash, responseData);
                        return;
                    } else {
                        this.scrollIntoView();
                        this.replaceContent(hash, responseData);

                        if (typeof hash !== 'undefined') {
                            window.location.hash = hash;
                        }
                    }

                    this.hideLoadingLayer($submitButton);
                } catch (err) {
                    console.error(err.message);

                    this.hideLoadingLayer($submitButton);
                }
            }).fail((jqXHR) => {
                this.hideLoadingLayer($submitButton);

                if (jqXHR.status === 400) {
                    this.handleFormErrorMessages($form, jqXHR.responseText);
                    this.scrollIntoView();
                } else if (jqXHR.responseText.length > 0) {
                    document.open();
                    document.write(jqXHR.responseText);
                    document.close();
                }
            });
        },
        showLoadingLayer: function ($submitButton) {
            let $loadingLayer = $('#loading-layer');

            if ($loadingLayer.length === 0) {
                const $body = $('body'),
                    loadingText = $(this.element).data('ajax-form-loading-text') || '',
                    html = '<div id="loading-layer" class="loading-layer"><h1 class="loading-layer__title"><span class="fa fa-cog fa-spin fa-fw"></span>' + loadingText + '</h1></div>';

                $(html).appendTo($body);

                setTimeout(() => {
                    $loadingLayer = $($loadingLayer.selector);

                    $loadingLayer.addClass(this.settings.loadingLayerActiveClass);
                }, 10);
            } else {
                $loadingLayer.addClass(this.settings.loadingLayerActiveClass);
            }

            if (typeof $submitButton !== 'undefined') {
                $submitButton.prop('disabled', true);
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

            this.findSubmitButton();
        },
        hideLoadingLayer: function ($submitButton) {
            $('#loading-layer').removeClass(this.settings.loadingLayerActiveClass);

            if (typeof $submitButton !== 'undefined') {
                $submitButton.prop('disabled', false);
            }
        },
        handleFormErrorMessages: function ($form, errorMessagesHtml) {
            const $errorBox = $('#error-box'),
                $modalBody = $form.find('.modal-body');

            $errorBox.remove();

            // Place the error messages inside the modal body for a better styling
            $(errorMessagesHtml)
                .hide()
                .prependTo(($modalBody.length > 0 && $modalBody.is(':visible')) ? $modalBody : $form)
                .fadeIn();

            this.prettyPrintResponseErrorMessages($($errorBox.selector));
        },
        prettyPrintResponseErrorMessages: function ($errorBox) {
            this.removeAllPreviousErrors();

            // highlight all input fields where the validation has failed
            $errorBox.find('li').each((index, element) => {
                const $this = $(element),
                    errorClass = $this.data('error');
                if (errorClass.length > 0) {
                    const $elem = $('[id|="' + errorClass + '"]').filter(':not([id$="container"])');
                    if ($elem.length > 0) {
                        this.addErrorDecorationToFormGroup($elem);

                        // Move the error message to the responsible input field(s)
                        // and remove the list item from the error box container
                        if ($elem.length === 1) {
                            this.addErrorMessageToFormField($elem, $this.html());
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
