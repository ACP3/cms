/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

((document) => {
  const socialSharingServices = document.getElementById("services");

  socialSharingServices.addEventListener("change", function (event) {
    if (event.detail?.init === true) {
      event.preventDefault();
    }

    document
      .getElementById("fb-credentials-wrapper")
      .classList.toggle("d-none", !Array.from(this.selectedOptions).some((option) => option.value === "facebook"));
  });

  socialSharingServices.dispatchEvent(new CustomEvent("change", { detail: { init: true } }));
})(document);
