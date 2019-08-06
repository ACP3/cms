/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

(($, window, document) => {
    'use strict';

    const pluginName = 'formSubmit';

    class AjaxForm {
        constructor(element, options) {
            this.element = element;
            this.isFormValid = true;

            this._defaults = {
                targetElement: '#content',
                loadingOverlay: true,
                loadingText: '',
                customFormData: null,
                scrollOffsetElement: null
            };
            this.settings = $.extend({}, this._defaults, options);
            this._name = pluginName;
            this.init();
        }

        init() {
            const that = this;

            this.mergeSettings();
            this.findSubmitButton();
            this.addLoadingLayer();
            this.element.noValidate = true;

            $(this.element).on('submit', (e) => {
                e.preventDefault();

                this.isFormValid = true;

                $(document).trigger('acp3.ajaxFrom.submit.before', [this]);

                if (this.isFormValid && this.preValidateForm(this.element)) {
                    this.processAjaxRequest();
                }
            }).on('click', function (e) {
                if ($(this).prop('tagName') === 'A') {
                    e.preventDefault();

                    that.processAjaxRequest();
                }
            }).on('change', () => {
                if (this.isFormValid === false) {
                    this.removeAllPreviousErrors();
                    this.checkFormElementsForErrors(this.element);
                }
            });
        }

        mergeSettings() {
            const data = $(this.element).data();
            for (let key in data) {
                if (Object.prototype.hasOwnProperty.call(data, key)) {
                    const keyStripped = this.lowerCaseFirstLetter(key.replace('ajaxForm', ''));

                    if (keyStripped.length > 0 && typeof this.settings[keyStripped] !== 'undefined') {
                        this.settings[keyStripped] = data[key];
                    }
                }
            }
        }

        lowerCaseFirstLetter(string) {
            return string.charAt(0).toLowerCase() + string.slice(1);
        }

        findSubmitButton() {
            $(this.element).find(':submit').click(function () {
                $(':submit', $(this).closest('form')).removeAttr('data-clicked');
                $(this).attr('data-clicked', 'true');
            });
        }

        preValidateForm(form) {
            this.removeAllPreviousErrors();
            this.checkFormElementsForErrors(form);
            this.focusTabWithFirstErrorMessage();
            this.scrollToFirstFormError();

            return this.isFormValid;
        }

        removeAllPreviousErrors() {
            $('form .form-group.has-error')
                .removeClass('has-error')
                .find('.validation-failed').remove();
        }

        checkFormElementsForErrors(form) {
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
        }

        addErrorDecorationToFormGroup($elem) {
            $elem.closest('.form-group').addClass('has-error');
        }

        removeErrorMessageFromFormField($elem) {
            $elem.closest('div').find('.validation-failed').remove();
        }

        addErrorMessageToFormField($element, errorMessage) {
            this.removeErrorMessageFromFormField($element);

            $element
                .closest('div:not(.input-group):not(.btn-group)')
                .append(`<small class="help-block validation-failed"><i class="glyphicon glyphicon-exclamation-sign"></i> ${errorMessage}</small>`
                );
        }

        focusTabWithFirstErrorMessage() {
            if ($('.tabbable').length > 0) {
                let $elem = $('.tabbable .form-group.has-error:first'),
                    tabId = $elem.closest('.tab-pane').prop('id');
                $('.tabbable .nav-tabs a[href="#' + tabId + '"]').tab('show');

                $elem.find(':input').focus();
            }
        }

        processAjaxRequest() {
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
                        if (Object.prototype.hasOwnProperty.call(this.settings.customFormData, key)) {
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

                        $(document).trigger('acp3.ajaxFrom.complete');

                        if (typeof hash !== 'undefined') {
                            window.location.hash = hash;
                        }
                    }
                } catch (e) {
                    console.error(e);
                }
            }).fail((jqXHR) => {
                if (jqXHR.status === 400) {
                    this.handleFormErrorMessages($form, jqXHR.responseText);
                    this.scrollToFirstFormError();

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
        }

        addLoadingLayer() {
            if (this.settings.loadingOverlay === false) {
                return;
            }

            const $loadingLayer = $('#loading-layer');

            if ($loadingLayer.length === 0) {
                const $body = $('body'),
                    loadingText = this.settings.loadingText || '',
                    html = `<div id="loading-layer" class="loading-layer"><h1><span class="glyphicon glyphicon-cog"></span> ${loadingText}</h1></div>`;

                $(html).appendTo($body);
            }
        }

        showLoadingLayer() {
            $('#loading-layer').addClass('loading-layer__active');
        }

        disableSubmitButton($submitButton) {
            if (typeof $submitButton !== 'undefined') {
                $submitButton.prop('disabled', true);
            }
        }

        enableSubmitButton($submitButton) {
            if (typeof $submitButton !== 'undefined') {
                $submitButton.prop('disabled', false);
            }
        }

        redirectToNewPage(hash, responseData) {
            if (typeof hash !== 'undefined') {
                window.location.href = responseData.redirect_url + hash;
                window.location.reload();
            } else {
                window.location.href = responseData.redirect_url;
            }
        }

        /**
         * Scroll to the beginning of the content area, if the current viewport is near the bottom
         */
        scrollIntoView() {
            const offsetTop = $(this.settings.targetElement).offset().top;

            if ($(document).scrollTop() > offsetTop) {
                $('html, body').animate(
                    {
                        scrollTop: offsetTop
                    },
                    'fast'
                );
            }
        }

        replaceContent(hash, responseData) {
            if (hash && $(hash).length) {
                $(hash).html($(responseData).find(hash).html());
            } else {
                $(this.settings.targetElement).html(responseData);
            }
        }

        rebindHandlers(hash) {
            const $bindingTarget = (hash && $(hash).length) ? $(hash) : $(this.settings.targetElement);

            $bindingTarget.find('[data-ajax-form="true"]').formSubmit();

            this.findSubmitButton();
        }

        hideLoadingLayer() {
            $('#loading-layer').removeClass('loading-layer__active');
        }

        handleFormErrorMessages($form, errorMessagesHtml) {
            let $errorBox = $('#error-box');
            const $modalBody = $form.find('.modal-body');

            $errorBox.remove();

            // Place the error messages inside the modal body for a better styling
            $errorBox = $(errorMessagesHtml);

            $errorBox
                .hide()
                .prependTo(($modalBody.length > 0 && $modalBody.is(':visible')) ? $modalBody : $form)
                .fadeIn();

            this.prettyPrintResponseErrorMessages($form, $errorBox);
        }

        prettyPrintResponseErrorMessages($form, $errorBox) {
            this.removeAllPreviousErrors();

            // highlight all input fields where the validation has failed
            $errorBox.find('li').each((index, element) => {
                let $this = $(element),
                    errorClass = $this.data('error');

                if (errorClass.length > 0) {
                    let $elem = $form.find('#' + errorClass) || $form.find('[id|="' + errorClass + '"]').filter(':not([id$="container"])');

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

        scrollToFirstFormError() {
            const $form = $(this.element);
            const $formErrors = $form.find('.form-group.has-error');

            if ($form.closest('.modal').length > 0) {
                return;
            }

            if (!$formErrors || $formErrors.length === 0) {
                return;
            }

            if (this.isElementInViewport($form.find('.help-block.validation-failed'))) {
                return;
            }

            let offsetTop = $formErrors.offset().top;

            if (this.settings.scrollOffsetElement) {
                const $scrollOffsetElement = $(this.settings.scrollOffsetElement);

                if ($scrollOffsetElement && $scrollOffsetElement.length > 0) {
                    offsetTop -= $scrollOffsetElement.height();
                }
            }

            $('html, body').animate(
                {
                    scrollTop: offsetTop
                },
                'fast'
            );
        }

        isElementInViewport(element) {
            // special bonus for those using jQuery
            if (typeof jQuery === 'function' && element instanceof jQuery) {
                element = element[0];
            }

            const $scrollOffsetElement = $(this.settings.scrollOffsetElement);
            let offsetTop = 0;

            if ($scrollOffsetElement) {
                offsetTop = $scrollOffsetElement.height();
            }

            const rect = element.getBoundingClientRect();

            return rect.top >= offsetTop
                && rect.left >= 0
                && rect.bottom <= (window.innerHeight || document.documentElement.clientHeight)
                && rect.right <= (window.innerWidth || document.documentElement.clientWidth);
        }
    }

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new AjaxForm(this, options));
            }
        });
    };
})(jQuery, window, document);

jQuery(document).ready(($) => {
    $('[data-ajax-form="true"]').formSubmit();

    $(document).on('draw.dt', (e) => {
        $(e.target).find('[data-ajax-form="true"]').formSubmit();
    });
});
