/*
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

;(function($) {
    "use strict";

    var pluginName = "suggestAlias",
        defaults = {
            prefix: '',
            slugBaseElement: null,
            aliasElement: null
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

            $(this.element).on('click', function(e) {
                e.preventDefault();

                that.performAjaxRequest();
            });
        },
        performAjaxRequest: function() {
            var that = this;

            if (that.settings.slugBaseElement !== null && that.settings.aliasElement !== null) {
                $.ajax({
                    url: that.element.href,
                    type: 'post',
                    data: {
                        prefix: that.settings.prefix,
                        title: that.settings.slugBaseElement.val()
                    },
                    beforeSend: function() {
                        $(that.element).addClass('disabled');
                    },
                    success: function (responseData) {
                        try {
                            if (typeof responseData.alias !== "undefined" && responseData.alias.length > 0) {
                                $(that.settings.aliasElement).val(responseData.alias);
                            }
                        } catch (err) {
                            console.log(err.message);
                        } finally {
                            $(that.element).removeClass('disabled');
                        }
                    }
                });
            }
        }
    });

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });
    };
})(jQuery);

jQuery(document).ready(function($) {
    $('#seo-alias-suggestion').suggestAlias({
        prefix: $('[data-seo-slug-prefix]').data('seo-slug-prefix'),
        slugBaseElement: $('[data-seo-slug-base="true"]'),
        aliasElement: $('#alias')
    });
});
