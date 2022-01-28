/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

((document) => {
  const dbName = document.getElementById("db-name");
  const formFields = document.querySelectorAll("#db-host, #db-user, #db-password");
  const ajaxUrl = dbName.dataset.availableDatabasesUrl;
  let formIsPrefilled = false;

  formFields.forEach((formField) => {
    formField.addEventListener("change", async () => {
      dbName.disabled = true;

      for (let i = 1; i < dbName.children.length; ++i) {
        dbName.children[i].remove();
      }

      formFields.forEach((formField) => {
        formField.classList.remove("is-invalid", "is-valid");
      });

      const formData = new FormData();
      formData.append("db_host", document.getElementById("db-host").value);
      formData.append("db_user", document.getElementById("db-user").value);
      formData.append("db_password", document.getElementById("db-password").value);

      try {
        const response = await fetch(ajaxUrl, {
          method: "POST",
          body: formData,
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
        });
        const responseData = await response.json();

        if (responseData.length > 0) {
          for (let i = 0; i < responseData.length; ++i) {
            dbName.insertAdjacentHTML(
              "beforeend",
              '<option value="' + responseData[i] + '">' + responseData[i] + "</option>"
            );
          }

          formFields.forEach((formField) => {
            formField.classList.add("is-valid");
          });
        } else {
          formFields.forEach((formField) => {
            formField.classList.add("is-invalid");
          });
        }
      } finally {
        dbName.disabled = false;
      }
    });

    if (formField.value && !formIsPrefilled) {
      formField.dispatchEvent(new InputEvent('change'));

      formIsPrefilled = true;
    }
  });
})(document);
