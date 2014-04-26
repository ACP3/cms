/**
 * Simple AJAX form handler
 *
 * @param loadingText
 */
jQuery.fn.formSubmit = function (loadingText) {
    var $this = jQuery(this);

    /**
     * Display a loading layer
     * @private
     */
    function _showLoadingLayer() {
        var $body = $('body'),
            documentHeight = $body.outerHeight(true);

        if ($('#loading-layer').length === 0) {
            $('<div id="loading-layer" style="height: ' + documentHeight + 'px"><h1><span class="glyphicon glyphicon-cog"></span>' + loadingText + '</h1></div>').appendTo($body).fadeIn();
        }
    }

    /**
     * Hides the loading layer
     *
     * @private
     */
    function _hideLoadingLayer() {
        $('#loading-layer').stop().fadeOut(function () {
            $(this).remove();
        });
    }

    $this.on('submit', function (e) {
        e.preventDefault();

        if (typeof CKEDITOR !== "undefined") {
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
        }

        $.ajax({
            url: $this.attr('action'),
            type: $this.attr('method').toUpperCase(),
            data: $this.serialize(),
            beforeSend: function () {
                _showLoadingLayer();
            },
            success: function (data) {
                try {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        if (data.success === false) {
                            $('#error-box').remove();
                            $(data.content).hide().prependTo($this).fadeIn();
                        } else if (data.success === true) {

                        } else {
                            var newDoc = document.open("text/html", "replace");
                            newDoc.write(data);
                            newDoc.close();
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
    });
};