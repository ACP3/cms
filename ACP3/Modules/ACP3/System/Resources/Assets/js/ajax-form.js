;(function ($, window, document) {
    "use strict";

    var pluginName = "formSubmit",
        defaults = {
            targetElement: '#content',
            customFormData: null
        };

    function Plugin(element, options) {
        this.element = element;

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

                $(document).trigger('acp3.ajaxFrom.submit.before');

                if (that.preValidateForm(that.element)) {
                    that.processAjaxRequest();
                }
            }).on('click', function (e) {
                if ($(this).prop('tagName') === 'A') {
                    e.preventDefault();

                    that.processAjaxRequest();
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
            var field,
                isValid = true;

            this.removeAllPreviousErrors();

            for (var i = 0; i < form.elements.length; i++) {
                field = form.elements[i];

                // ignore buttons, fieldsets, etc.
                if (field.nodeName !== "INPUT" && field.nodeName !== "TEXTAREA" && field.nodeName !== "SELECT") {
                    continue;
                }

                if (!field.checkValidity()) {
                    this.addErrorDecorationToFormGroup($(field));
                    this.addErrorMessageToFormField($(field), field.validationMessage);

                    isValid = false;
                }
            }

            this.focusTabWithFirstErrorMessage();

            return isValid;
        },
        removeAllPreviousErrors: function () {
            $('form .form-group.has-error')
                .removeClass('has-error')
                .find('.validation-failed').remove();
        },
        addErrorDecorationToFormGroup: function ($elem) {
            $elem.closest('.form-group').addClass('has-error');
        },
        removeErrorMessageFromFormField: function ($elem) {
            $elem.closest('div').find('.validation-failed').remove();
        },
        addErrorMessageToFormField: function (formField, errorMessage) {
            this.removeErrorMessageFromFormField(formField);

            formField
                .closest('div:not(.input-group)')
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
            var that = this,
                $form = $(this.element),
                hasCustomData = !$.isEmptyObject(this.settings.customFormData),
                data = this.settings.customFormData || {};

            if ($form.attr('method')) {
                var $submitButton = $(':submit[data-clicked="true"]', $form),
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
            }

            $.ajax({
                url: $form.attr('action') || $form.attr('href'),
                type: $form.attr('method') ? $form.attr('method').toUpperCase() : 'GET',
                data: data,
                processData: hasCustomData,
                contentType: (hasCustomData) ? 'application/x-www-form-urlencoded; charset=UTF-8' : false,
                beforeSend: function () {
                    that.showLoadingLayer();
                },
                success: function (responseData) {
                    try {
                        if (responseData.redirect_url) {
                            window.location.href = responseData.redirect_url;
                        } else {
                            var $content = $(that.settings.targetElement),
                                offsetTop = $content.offset().top;

                            // Scroll to the beginning of the content area, if the current viewport is near the bottom
                            if ($(document).scrollTop() > offsetTop) {
                                $('html, body').animate(
                                    {
                                        scrollTop: offsetTop
                                    },
                                    'fast'
                                );
                            }

                            if (responseData.success === false) {
                                that.handleFormErrorMessages($form, responseData.content);
                            } else {
                                $content.html(responseData);

                                if (typeof hash !== "undefined") {
                                    location.hash = hash;
                                }
                            }
                        }
                    } catch (err) {
                        console.log(err.message);
                    } finally {
                        that.hideLoadingLayer();
                    }
                }
            });
        },
        showLoadingLayer: function () {
            var $loadingLayer = $('#loading-layer');

            if ($loadingLayer.length === 0) {
                var $body = $('body'),
                    loadingText = $(this.element).data('ajax-form-loading-text') || '',
                    windowHeight = $(window).outerHeight(true),
                    html = '<div id="loading-layer" style="height: ' + windowHeight + 'px"><h1><span class="glyphicon glyphicon-cog"></span>' + loadingText + '</h1></div>';

                $(html).appendTo($body);

                $loadingLayer = $($loadingLayer.selector);

                $loadingLayer.show();
                var $heading = $loadingLayer.find('h1'),
                    headingHeight = $heading.height();

                $heading.css({
                    marginTop: (Math.round(windowHeight / 2) - headingHeight) + 'px'
                });

                $loadingLayer.hide().fadeIn();
            } else {
                $loadingLayer.fadeIn();
            }
        },
        hideLoadingLayer: function () {
            $('#loading-layer').stop().fadeOut();
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
                        if ($elem.length == 1) {
                            that.addErrorMessageToFormField($elem, $this.html());
                            $this.remove();
                        }
                    }
                }
            });

            // if all list items have been removed, remove the error box container too
            if ($errorBox.find('li').length == 0) {
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
