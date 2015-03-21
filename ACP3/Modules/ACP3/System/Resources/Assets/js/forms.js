/**
 * Simple AJAX form handler
 *
 * @param [customFormData]
 */
jQuery.fn.formSubmit = function (customFormData) {
    /**
     * Displays a loading layer
     * @private
     */
    function _showLoadingLayer($form) {
        var $loadingLayer = $('#loading-layer');

        if ($loadingLayer.length === 0) {
            var $body = $('body'),
                loadingText = $form.data('ajax-form-loading-text') || '',
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
    }

    /**
     * Hides the loading layer
     *
     * @private
     */
    function _hideLoadingLayer() {
        $('#loading-layer').stop().fadeOut();
    }

    /**
     * Processes the AJAX requests
     *
     * @param $form
     * @param [customFormData]
     * @private
     */
    function _processAjaxRequest($form, customFormData) {
        var url = $form.attr('action') || $form.attr('href'),
            processData = (customFormData) ? true : false,
            contentType = (customFormData) ? 'application/x-www-form-urlencoded; charset=UTF-8' : false,
            type,
            data;

        if ($form.attr('method')) {
            type = $form.attr('method').toUpperCase();
            data = customFormData || new FormData($form[0]);
        } else {
            type = 'GET';
            data = customFormData || {};
        }

        $.ajax({
            url: url,
            type: type,
            data: data,
            processData: processData,
            contentType: contentType,
            beforeSend: function () {
                _showLoadingLayer($form);
            },
            success: function (data) {
                try {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        var $content = $('#content'),
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

                        if (data.success === false) { // An error has occurred
                            $('#error-box').remove();
                            var $modalBody = $form.find('.modal-body');
                            // Place the error messages inside the modal body for a better styling
                            $(data.content)
                                .hide()
                                .prependTo(($modalBody.length > 0 && $modalBody.is(':visible')) ? $modalBody : $form)
                                .fadeIn();
                        } else { // The request was successful
                            $content.html(data);
                        }
                    }
                } catch (err) {
                    if (typeof console !== "undefined") {
                        console.log(err.message);
                    }
                }
            },
            complete: function () {
                _hideLoadingLayer();
            }
        });
    }

    $(this).each(function () {
        var $this = $(this);

        $this.on('submit', function (e) {
            e.preventDefault();

            if (typeof CKEDITOR !== "undefined") {
                for (var instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
            }

            _processAjaxRequest($this, customFormData);
        }).on('click', function (e) {
            if ($this.prop('tagName') === 'A') {
                e.preventDefault();

                _processAjaxRequest($this, customFormData);
            }
        });
    });
};

jQuery(document).ready(function ($) {
    $('[data-ajax-form="true"]').formSubmit();
});