/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */
(($) => {
  const $dbName = $("#db-name"),
    $formFields = $("#db-host, #db-user, #db-password"),
    $formGroup = $formFields.closest(".form-group"),
    ajaxUrl = $dbName.data("available-databases-url");

  $formFields
    .on("change", () => {
      $dbName.prop("disabled", true).find("option").not(":first").remove();
      $formGroup.removeClass("has-success").removeClass("has-error");

      $.post(
        ajaxUrl,
        {
          db_host: $("#db-host").val(),
          db_user: $("#db-user").val(),
          db_password: $("#db-password").val(),
        },
        function (response) {
          if (response.length > 0) {
            for (let i = 0; i < response.length; ++i) {
              $dbName.append('<option value="' + response[i] + '">' + response[i] + "</option>");
            }

            $formGroup.addClass("has-success");
          } else {
            $formGroup.addClass("has-error");
          }
        }
      ).always(function () {
        $dbName.prop("disabled", false);
      });
    })
    .triggerHandler("change");
})(jQuery);
