/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

function mergeSettings(defaultSettings, constructorOptions, dataAttributeOptions) {
    const mergedSettings = {...defaultSettings, ...constructorOptions};

    for (let [key, value] of Object.entries(dataAttributeOptions)) {
        const keyStripped = lowerCaseFirstLetter(key.replace('ajaxForm', ''));

        if (keyStripped.length > 0 && typeof mergedSettings[keyStripped] !== 'undefined') {
            mergedSettings[keyStripped] = value;
        }
    }

    return mergedSettings;
}

function lowerCaseFirstLetter(string) {
    return string.charAt(0).toLowerCase() + string.slice(1);
}

export class LoadingLayer {
    #options = {
        loadingText: '',
    };

    constructor(element, options) {
        this.#options = mergeSettings(this.#options, options, jQuery(element).data() || {});
    }

    addLoadingLayer() {
        if (document.getElementById('loading-layer')?.length === 0) {
            const $body = jQuery('body'),
                html = '<div id="loading-layer" class="loading-layer"><h1><span class="fas fa-spinner fa-spin"></span> ' + this.#options.loadingText + '</h1></div>';

            jQuery(html).appendTo($body);
        }
    }

    showLoadingLayer() {
        this.#toggleLoadingLayer(true);
    }

    hideLoadingLayer() {
        this.#toggleLoadingLayer(false);
    }

    #toggleLoadingLayer(show) {
        document.getElementById('loading-layer')?.classList.toggle('loading-layer__active', show);
    }
}

class FormValidator {
    #isFormValid = true;

    preValidateForm(form) {
        this.removeAllPreviousErrors();
        this.checkFormElementsForErrors(form);
        this.#focusTabWithFirstErrorMessage();
        this.#scrollToFirstFormError();

        return this.#isFormValid;
    }

    removeAllPreviousErrors() {
        jQuery('form .form-group.has-error')
            .removeClass('has-error')
            .find('.validation-failed').remove();
    }

    checkFormElementsForErrors(form) {
        for (const field of form.elements) {
            if (field.nodeName !== 'INPUT' && field.nodeName !== 'TEXTAREA' && field.nodeName !== 'SELECT') {
                continue;
            }

            if (!field.checkValidity()) {
                this.#addErrorDecorationToFormGroup(jQuery(field));
                this.#addErrorMessageToFormField(jQuery(field), field.validationMessage);

                this.#isFormValid = false;
            }
        }
    }

    #addErrorDecorationToFormGroup($elem) {
        $elem.closest('.form-group').addClass('has-error');
    }

    #removeErrorMessageFromFormField($elem) {
        $elem.closest('div').find('.validation-failed').remove();
    }

    #addErrorMessageToFormField($element, errorMessage) {
        this.#removeErrorMessageFromFormField($element);

        $element
            .closest('div:not(.input-group):not(.btn-group)')
            .append('<small class="help-block validation-failed"><i class="fas fa-exclamation-circle"></i> ' + errorMessage + '</small>'
            );
    }

    #focusTabWithFirstErrorMessage() {
        if (jQuery('.tabbable').length > 0) {
            let $elem = jQuery('.tabbable .form-group.has-error:first'),
                tabId = $elem.closest('.tab-pane').prop('id');
            jQuery('.tabbable .nav-tabs a[href="#' + tabId + '"]').tab('show');

            $elem.find(':input').focus();
        }
    }

    get isFormValid() {
        return this.#isFormValid;
    }

    setFormAsValid() {
        this.#isFormValid = true;
    }

    handleFormErrorMessages($form, errorMessagesHtml) {
        let $errorBox = jQuery('#error-box');
        const $modalBody = $form.find('.modal-body');

        $errorBox.remove();

        // Place the error messages inside the modal body for a better styling
        $errorBox = jQuery(errorMessagesHtml);

        $errorBox
            .hide()
            .prependTo(($modalBody.length > 0 && $modalBody.is(':visible')) ? $modalBody : $form)
            .fadeIn();

        this.#prettyPrintResponseErrorMessages($form, $errorBox);
    }

    #prettyPrintResponseErrorMessages($form, $errorBox) {
        this.removeAllPreviousErrors();

        // highlight all input fields where the validation has failed
        $errorBox.find('li').each((index, element) => {
            let $this = jQuery(element),
                errorClass = $this.data('error');

            if (errorClass.length > 0) {
                let $elem = $form.find('#' + errorClass) || $form.find('[id|="' + errorClass + '"]').filter(':not([id$="container"])');

                if ($elem.length > 0) {
                    this.#addErrorDecorationToFormGroup($elem);

                    // Move the error message to the responsible input field(s)
                    // and remove the list item from the error box container
                    if ($elem.length === 1) {
                        this.#addErrorMessageToFormField($elem, $this.html());
                        $this.remove();
                    }
                }
            }
        });

        // if all list items have been removed, remove the error box container too
        if ($errorBox.find('li').length === 0) {
            $errorBox.remove();
        }

        this.#focusTabWithFirstErrorMessage();
        this.#scrollToFirstFormError();
    }

    #scrollToFirstFormError() {
        const $form = jQuery(this.element);
        const $formErrors = $form.find('.form-group.has-error');

        if ($form.closest('.modal').length > 0) {
            return;
        }

        if (!$formErrors || $formErrors.length === 0) {
            return;
        }

        if (this.#isElementInViewport($form.find('.help-block.validation-failed'))) {
            return;
        }

        let offsetTop = $formErrors.offset().top;

        if (this.settings.scrollOffsetElement) {
            const $scrollOffsetElement = jQuery(this.settings.scrollOffsetElement);

            if ($scrollOffsetElement && $scrollOffsetElement.length > 0) {
                offsetTop -= $scrollOffsetElement.height();
            }
        }

        jQuery('html, body').animate(
            {
                scrollTop: offsetTop
            },
            'fast'
        );
    }

    #isElementInViewport(element) {
        // special bonus for those using jQuery
        if (typeof jQuery === 'function' && element instanceof jQuery) {
            element = element[0];
        }

        const $scrollOffsetElement = jQuery(this.settings.scrollOffsetElement);
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

export class AjaxForm {
    #loadingLayer;
    #formValidator = new FormValidator();

    /**
     *
     * @param {HTMLElement} element
     * @param {LoadingLayer} loadingLayer
     * @param {object} options
     */
    constructor(element, loadingLayer, options) {
        this.element = element;
        this.isFormValid = true;

        this.#loadingLayer = loadingLayer;

        this._defaults = {
            targetElement: '#content',
            loadingOverlay: true,
            customFormData: null,
            scrollOffsetElement: null,
            method: null,
        };
        this.settings = mergeSettings(this._defaults, options, jQuery(element).data());
        this.#init();
    }

    #init() {
        const that = this;

        this.findSubmitButton();
        this.#loadingLayer.addLoadingLayer();
        this.element.noValidate = true;

        jQuery(this.element).on('submit', (e) => {
            e.preventDefault();

            this.#formValidator.setFormAsValid();

            jQuery(document).trigger('acp3.ajaxFrom.submit.before', [this]);

            if (this.#formValidator.isFormValid && this.#formValidator.preValidateForm(this.element)) {
                this.performAjaxRequest();
            }
        }).on('click', function (e) {
            if (jQuery(this).prop('tagName') === 'A') {
                e.preventDefault();

                that.performAjaxRequest();
            }
        }).on('change', () => {
            if (this.#formValidator.isFormValid === false) {
                this.#formValidator.removeAllPreviousErrors();
                this.#formValidator.checkFormElementsForErrors(this.element);
            }
        });
    }

    findSubmitButton() {
        jQuery(this.element).find(':submit').click(function () {
            jQuery(':submit', jQuery(this).closest('form')).removeAttr('data-clicked');
            jQuery(this).attr('data-clicked', 'true');
        });
    }

    async performAjaxRequest() {
        const $form = jQuery(this.element);

        let hash,
            $submitButton;

        if ($form.attr('method')) {
            $submitButton = jQuery(':submit[data-clicked="true"]', $form);

            hash = $submitButton.data('hashChange');
        } else {
            hash = $form.data('hashChange');
        }

        this.#loadingLayer.showLoadingLayer();
        this.disableSubmitButton($submitButton);

        try {
            const response = await fetch($form.attr('action') || $form.attr('href'), {
                method: $form.attr('method') ? $form.attr('method').toUpperCase() : this.settings.method?.toUpperCase() ?? 'GET',
                body: this.prepareFormData($form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                await this.handleResponseError(response, $form);
            } else {
                await this.handleSuccessfulResponse(response, $form, hash);
            }
        } catch (error) {
            console.error(error);
        } finally {
            this.#loadingLayer.hideLoadingLayer();
            this.enableSubmitButton($submitButton);
        }
    }

    /**
     *
     * @param {jQuery} $form
     * @returns {FormData}
     */
    prepareFormData($form) {
        const initialData = this.settings.customFormData || {};
        let data;

        if ($form.attr('method')) {
            const $submitButton = jQuery(':submit[data-clicked="true"]', $form);

            data = new FormData($form[0]);

            if ($submitButton.length) {
                data.append($submitButton.attr('name'), '1');
            }
        } else {
            data = new FormData();
        }

        for (let [key, value] of Object.entries(initialData)) {
            data.append(key, value);
        }

        return data;
    }

    /**
     *
     * @param {Response} response
     * @param {jQuery} $form
     * @returns {Promise<void>}
     */
    async handleResponseError(response, $form) {
        const responseData = await response.clone().text();

        if (response.status === 400) {
            this.#formValidator.handleFormErrorMessages($form, responseData);

            jQuery(document).trigger('acp3.ajaxFrom.submit.fail', [this]);
        } else if (responseData.length > 0) {
            document.open();
            document.write(responseData);
            document.close();
        }
    }

    /**
     *
     * @param {Response} response
     * @param {jQuery} $form
     * @param {string} hash
     * @returns {Promise<void>}
     */
    async handleSuccessfulResponse(response, $form, hash) {
        const responseData = await this.decodeResponse(response);

        let callback = $form.data('ajax-form-complete-callback');

        if (typeof window[callback] === 'function') {
            window[callback](responseData);
        } else if (responseData.redirect_url) {
            this.redirectToNewPage(hash, responseData);
        } else {
            this.scrollIntoView();
            this.replaceContent(hash, responseData);
            this.rebindHandlers(hash);

            jQuery(document).trigger('acp3.ajaxFrom.complete');

            if (typeof hash !== 'undefined') {
                window.location.hash = hash;
            }
        }
    }

    addLoadingLayer() {
        if (this.settings.loadingOverlay === false) {
            return;
        }

        this.#loadingLayer.addLoadingLayer();
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

    /**
     *
     * @param {Response} response
     * @returns {Promise<*>}
     */
    async decodeResponse(response) {
        try {
            return await response.clone().json();
        } catch (error) {
            return await response.clone().text();
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
        const offsetTop = jQuery(this.settings.targetElement).offset().top;

        if (jQuery(document).scrollTop() > offsetTop) {
            jQuery('html, body').animate(
                {
                    scrollTop: offsetTop
                },
                'fast'
            );
        }
    }

    replaceContent(hash, responseData) {
        if (hash && jQuery(hash).length) {
            jQuery(hash).html(jQuery(responseData).find(hash).html());
        } else {
            jQuery(this.settings.targetElement).html(responseData);
        }
    }

    rebindHandlers(hash) {
        const $bindingTarget = (hash && jQuery(hash).length) ? jQuery(hash) : jQuery(this.settings.targetElement);

        $bindingTarget.find('[data-ajax-form="true"]').formSubmit();

        this.findSubmitButton();
    }
}
