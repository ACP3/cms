/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

import { AjaxForm, LoadingLayer } from "../lib/ajax-form";

(($) => {
  const pluginName = "formSubmit";

  $.fn[pluginName] = function (options) {
    return this.each(function () {
      if (!$.data(this, "plugin_" + pluginName)) {
        $.data(this, "plugin_" + pluginName, new AjaxForm(this, new LoadingLayer(this, options), options));
      }
    });
  };

  $('[data-ajax-form="true"]').formSubmit();

  $(document).on("draw.dt", (e) => {
    $(e.target).find('[data-ajax-form="true"]').formSubmit();
  });
})(jQuery);
