/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

import Modal from "bootstrap/js/dist/modal";
import { addScriptsToHead } from "../lib/utils";

((document) => {
  const ajaxModals = document.querySelectorAll("[data-ajax-modal]");

  ajaxModals.forEach((ajaxModal) => {
    const createLinkElem = document.getElementById(ajaxModal.id + "-link");
    const modal = new Modal(ajaxModal);

    createLinkElem.addEventListener("click", async (e) => {
      e.preventDefault();

      modal.show();

      if (ajaxModal.classList.contains("js-replaced-content")) {
        return;
      }

      const response = await fetch(createLinkElem.href, {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      const modalContentElem = ajaxModal.querySelector(".modal-content");
      modalContentElem.innerHTML = await response.text();
      addScriptsToHead(modalContentElem);

      ajaxModal.classList.add("js-replaced-content");
    });
  });
})(document);
