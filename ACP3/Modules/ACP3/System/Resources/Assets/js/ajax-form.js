/*
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

;(function ($, window, document) {
    "use strict";

    var pluginName = "formSubmit",
        defaults = {
            targetElement: '#content',
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
            var that = this;

            this.findSubmitButton();
            this.element.noValidate = true;

            $(this.element).on('submit', function (e) {
                e.preventDefault();

                that.isFormValid = true;

                $(document).trigger('acp3.ajaxFrom.submit.before', [ that ]);

                if (that.isFormValid && that.preValidateForm(that.element)) {
                    that.processAjaxRequest();
                }
            }).on('click', function (e) {
                if ($(this).prop('tagName') === 'A') {
                    e.preventDefault();

                    that.processAjaxRequest();
                }
            });

            $(this.element).change(function() {
                if (that.isFormValid === false) {
                    that.removeAllPreviousErrors();
                    that.checkFormElementsForErrors(that.element);
                }
            });
        },
        findSubmitButton: function () {
            $(this.element).find(':submit').click(function () {
                $(":submit", $(this).closest("form")).removeAttr("data-clicked");
                $(this).attr("data-clicked", "true");
            });
        },
        preValidateForm: function (form) {
            this.removeAllPreviousErrors();
            this.checkFormElementsForErrors(form);
            this.focusTabWithFirstErrorMessage();

            return this.isFormValid;
        },
        removeAllPreviousErrors: function () {
            $('form .form-group.has-error')
                .removeClass('has-error')
                .find('.validation-failed').remove();
        },
        checkFormElementsForErrors: function(form) {
            var field;
            for (var i = 0; i < form.elements.length; i++) {
                field = form.elements[i];

                if (field.nodeName !== "INPUT" && field.nodeName !== "TEXTAREA" && field.nodeName !== "SELECT") {
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
                    '<small class="help-block validation-failed"><i class="glyphicon glyphicon-remove"></i> ' + errorMessage + '</small>'
                );
        },
        focusTabWithFirstErrorMessage: function () {
            if ($('.tabbable').length > 0) {
                var $elem = $('.tabbable .form-group.has-error:first'),
                    tabId = $elem.closest('.tab-pane').prop('id');
                $('.tabbable .nav-tabs a[href="#' + tabId + '"]').tab('show');

                $elem.find(':input').focus();
            }
        },
        processAjaxRequest: function () {
            var hash,
                self = this,
                $form = $(this.element),
                hasCustomData = !$.isEmptyObject(this.settings.customFormData),
                processData = true,
                data = this.settings.customFormData || {};

            if ($form.attr('method')) {
                var $submitButton = $(':submit[data-clicked="true"]', $form);

                hash = $submitButton.data('hashChange');

                data = new FormData($form[0]);

                if ($submitButton.length) {
                    data.append($submitButton.attr('name'), 1);
                }

                if (hasCustomData) {
                    for (var key in this.settings.customFormData) {
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
                beforeSend: function () {
                    self.showLoadingLayer($submitButton);
                }
            }).done(function (responseData) {
                try {
                    var callback = $form.data('ajax-form-complete-callback');

                    if (typeof window[callback] === 'function') {
                        window[callback](responseData);
                    } else {
                        if (responseData.redirect_url) {
                            if (typeof hash !== "undefined") {
                                window.location.href = responseData.redirect_url + hash;
                                window.location.reload();
                            } else {
                                window.location.href = responseData.redirect_url;
                            }
                            return;
                        }

                        self.scrollIntoView();

                        $(self.settings.targetElement).html(responseData);

                        if (typeof hash !== "undefined") {
                            window.location.hash = hash;
                        }
                    }

                    self.hideLoadingLayer($submitButton);
                } catch (err) {
                    console.log(err.message);

                    self.hideLoadingLayer($submitButton);
                }
            }).fail(function (jqXHR) {
                self.hideLoadingLayer($submitButton);

                if (jqXHR.status === 400) {
                    self.handleFormErrorMessages($form, jqXHR.responseText);
                    self.scrollIntoView();
                } else if (jqXHR.responseText.length > 0) {
                    document.open();
                    document.write(jqXHR.responseText);
                    document.close();
                }
            });
        },
        showLoadingLayer: function ($submitButton) {
            var $loadingLayer = $('#loading-layer');

            if ($loadingLayer.length === 0) {
                var $body = $('body'),
                    loadingText = $(this.element).data('ajax-form-loading-text') || '',
                    html = '<div id="loading-layer" class="loading-layer"><h1><span class="glyphicon glyphicon-cog"></span>' + loadingText + '</h1></div>';

                $(html).appendTo($body);

                setTimeout(function() {
                    $loadingLayer = $($loadingLayer.selector);

                    $loadingLayer.addClass('loading-layer__active');
                }, 10);
            } else {
                $loadingLayer.addClass('loading-layer__active');
            }

            if (typeof $submitButton !== "undefined") {
                $submitButton.prop('disabled', true);
            }
        },
        /**
         * Scroll to the beginning of the content area, if the current viewport is near the bottom
         */
        scrollIntoView: function () {
            var offsetTop = $(this.settings.targetElement).offset().top;

            if ($(document).scrollTop() > offsetTop) {
                $('html, body').animate(
                    {
                        scrollTop: offsetTop
                    },
                    'fast'
                );
            }
        },
        hideLoadingLayer: function ($submitButton) {
            $('#loading-layer').removeClass('loading-layer__active');

            if (typeof $submitButton !== "undefined") {
                $submitButton.prop('disabled', false);
            }
        },
        handleFormErrorMessages: function ($form, errorMessagesHtml) {
            var $errorBox = $('#error-box'),
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
            var that = this;

            this.removeAllPreviousErrors();

            // highlight all input fields where the validation has failed
            $errorBox.find('li').each(function () {
                var $this = $(this),
                    errorClass = $this.data('error');
                if (errorClass.length > 0) {
                    var $elem = $('[id|="' + errorClass + '"]').filter(':not([id$="container"])');
                    if ($elem.length > 0) {
                        that.addErrorDecorationToFormGroup($elem);

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
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });
    };
})(jQuery, window, document);

jQuery(document).ready(function ($) {
    $('[data-ajax-form="true"]').formSubmit();
});
