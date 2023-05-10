/* global wysiwygCallback */

import Modal from "bootstrap/js/dist/modal";

((document) => {
  const pageBreakFormEl = document.getElementById("page-break-form");
  const modal = new Modal(pageBreakFormEl);

  document
    .getElementById("page-break-form")
    .querySelector(".modal-footer button.btn-primary")
    .addEventListener("click", function (e) {
      e.preventDefault();

      const tocTitle = document.getElementById("toc-title");
      let text;

      if (tocTitle.value.length > 0) {
        text = '<hr class="page-break" title="' + tocTitle.value + '" />';
      } else {
        text = '<hr class="page-break" />';
      }

      wysiwygCallback(text);

      modal.hide();
    });
})(document);
