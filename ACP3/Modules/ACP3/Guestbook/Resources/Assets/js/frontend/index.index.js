/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

/* global bootstrap */

import { addScriptsToHead } from "../../../../../System/Resources/Assets/js/lib/utils";

((document) => {
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

    const modalContentElem = modalElem.querySelector(".modal-content");
    modalContentElem.innerHTML = await response.text();
    addScriptsToHead(modalContentElem);

    modalElem.classList.add("js-replaced-content");
  });
})(document);
