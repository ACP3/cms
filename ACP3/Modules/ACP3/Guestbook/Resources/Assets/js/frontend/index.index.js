/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

(($) => {
  $("#modal-create").on("shown.bs.modal", function () {
    $(this).find('[data-ajax-form="true"]').formSubmit();
  });
})(jQuery);
