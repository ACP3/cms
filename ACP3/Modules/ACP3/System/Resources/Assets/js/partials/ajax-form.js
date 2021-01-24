/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

import { AjaxForm } from "../lib/ajax-form";
import { LoadingLayer } from "../lib/loading-layer";
import { FormValidator } from "../lib/form-validator";

(($) => {
  const pluginName = "formSubmit";

  $.fn[pluginName] = function (options) {
    return this.each(function () {
      if (!$.data(this, "plugin_" + pluginName)) {
        $.data(
          this,
          "plugin_" + pluginName,
          new AjaxForm(this, new LoadingLayer(this, options), new FormValidator(), options)
        );
      }
    });
  };

  $('[data-ajax-form="true"]').formSubmit();

  $(document).on("draw.dt", (e) => {
    $(e.target).find('[data-ajax-form="true"]').formSubmit();
  });
})(jQuery);
