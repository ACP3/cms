/**
 * Simple AJAX form handler
 *
 * @param [customFormData]
 */
jQuery.fn.formSubmit = function (customFormData) {
    /**
     * Display a loading layer
     * @private
     */
    function _showLoadingLayer($form) {
        var $body = $('body'),
            loadingText = $form.data('ajax-form-loading-text') || '',
            $loadingLayer = $('#loading-layer'),
            documentHeight = $body.outerHeight(true);

        if ($loadingLayer.length === 0) {
            $('<div id="loading-layer" style="height: ' + documentHeight + 'px"><h1><span class="glyphicon glyphicon-cog"></span>' + loadingText + '</h1></div>').appendTo($body);

            $loadingLayer = $('#loading-layer');

            $loadingLayer.show();
            var windowHeight = $(window).outerHeight(true),
                $heading = $loadingLayer.find('h1'),
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
                        if (data.success === false) {
                            $('#error-box').remove();
                            $(data.content).hide().prependTo($form).fadeIn();
                        } else {
                            $('#content').html(data);
                        }
                    }
                } catch (err) {
                    console.log(err.message);
                }
            },
            complete: function () {
                _hideLoadingLayer();
            }
        });
    }

    $(this).each(function () {
        var $this = $(this);

        $this.on('submit',function (e) {
            e.preventDefault();

            if (typeof CKEDITOR !== "undefined") {
                for (instance in CKEDITOR.instances) {
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