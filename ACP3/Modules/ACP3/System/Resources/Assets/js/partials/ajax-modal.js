/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

import Modal from "bootstrap/js/dist/modal";
import { addScriptsToHead, addStylesheetToHead } from "../lib/utils";

((document) => {
  const ajaxModals = document.querySelectorAll("[data-ajax-modal]");

  ajaxModals.forEach((ajaxModal) => {
    const createLinkElem = document.getElementById(ajaxModal.id + "-link");
    const modal = new Modal(ajaxModal, { backdrop: "static", focus: false });

    createLinkElem.addEventListener("click", async (e) => {
      e.preventDefault();

      modal.show();

      if (ajaxModal.classList.contains("js-replaced-content")) {
        return;
      }

      const response = await fetch(createLinkElem.href, {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "X-ACP3-Content-Type": "ajax-modal",
        },
      });

      const modalContentElem = ajaxModal.querySelector(".modal-content");
      modalContentElem.innerHTML = await response.text();
      addScriptsToHead(modalContentElem);
      addStylesheetToHead(modalContentElem);

      ajaxModal.classList.add("js-replaced-content");
    });
  });
})(document);
