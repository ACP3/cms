/* global wysiwygCallback */

(($) => {
  $("#page-break-form")
    .find(".modal-footer button.btn-primary")
    .click(function (e) {
      e.preventDefault();

      const $tocTitle = $("#toc-title");
      let text;

      if ($tocTitle.val().length > 0) {
        text = '<hr class="page-break" title="' + $tocTitle.val() + '" />';
      } else {
        text = '<hr class="page-break" />';
      }

      wysiwygCallback(text);
      $("#page-break-form").modal("hide");
    });
})(jQuery);
