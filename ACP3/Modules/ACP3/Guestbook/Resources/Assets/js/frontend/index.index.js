/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

// @ToDO: Remove jQuery
(($, document) => {
  const modal = document.getElementById("modal-create");
  modal.addEventListener("shown.bs.modal", function () {
    $(modal).find('[data-ajax-form="true"]').formSubmit();
  });
})(jQuery, document);
