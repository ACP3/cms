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
    function showLoadingLayer($form) {
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
    function hideLoadingLayer() {
        $('#loading-layer').stop().fadeOut();
    }

    /**
     *
     * @param $form
     * @private
     */
    function findSubmitButton($form) {
        $form.find(':submit').click(function () {
            $(":submit", $(this).closest("form")).removeAttr("data-clicked");
            $(this).attr("data-clicked", "true");
        });
    }

    /**
     * Processes the AJAX requests
     *
     * @param $form
     * @param [customFormData]
     * @private
     */
    function processAjaxRequest($form, customFormData) {
        var url = $form.attr('action') || $form.attr('href'),
            processData = (customFormData) ? true : false,
            contentType = (customFormData) ? 'application/x-www-form-urlencoded; charset=UTF-8' : false,
            type,
            data;

        if ($form.attr('method')) {
            var $submitButton = $(':submit[data-clicked="true"]', $form),
                hash = $submitButton.data('hashChange');

            type = $form.attr('method').toUpperCase();
            data = new FormData($form[0]);

            if ($submitButton.length) {
                data.append($submitButton.attr('name'), 1);
            }

            if (customFormData) {
                for (var key in customFormData) {
                    if (customFormData.hasOwnProperty(key)) {
                        data.append(key, customFormData[key]);
                    }
                }
            }
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
                showLoadingLayer($form);
            },
            success: function (data) {
                try {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        hideLoadingLayer();

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

                            if (hash) {
                                location.hash = hash;
                            }
                        }
                    }
                } catch (err) {
                    hideLoadingLayer();

                    if (typeof console !== "undefined") {
                        console.log(err.message);
                    }
                }
            }
        });
    }

    $(this).each(function () {
        var $this = $(this);

        findSubmitButton($this);
        $this.on('submit', function (e) {
            e.preventDefault();

            $(document).trigger('acp3.ajaxFrom.submit.before');

            processAjaxRequest($this, customFormData);
        }).on('click', function (e) {
            if ($this.prop('tagName') === 'A') {
                e.preventDefault();

                processAjaxRequest($this, customFormData);
            }
        });
    });
};

jQuery(document).ready(function ($) {
    $('[data-ajax-form="true"]').formSubmit();
});
