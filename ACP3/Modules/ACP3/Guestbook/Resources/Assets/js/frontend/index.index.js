/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

/* global bootstrap */

// @ToDO: Remove jQuery
(($, document) => {
  const createLinkElem = document.getElementById("js-create-link");
  const modalElem = document.getElementById("js-modal-create");
  const modal = new bootstrap.Modal(modalElem);

  createLinkElem.addEventListener("click", async (e) => {
    e.preventDefault();

    modal.show();

    if (modalElem.classList.contains("js-replaced-content")) {
      return;
    }

    const response = await fetch(createLinkElem.href, {
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    });

    modalElem.querySelector(".modal-content").innerHTML = await response.text();
    modalElem.classList.add("js-replaced-content");

    $(modalElem).find('[data-ajax-form="true"]').formSubmit();
  });
})(jQuery, document);
