/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

((document) => {
  const shareServicesWrapper = document.getElementById("share-services-wrapper");
  const shareCustomizeServicesWrapper = document.getElementById("share-custom-services-wrapper");

  document.querySelectorAll('[name="share_active"]').forEach((elem) => {
    elem.addEventListener("change", () => {
      shareServicesWrapper.classList.toggle("d-none", Number(elem.value) === 0);
    });

    if (elem.checked) {
      elem.dispatchEvent(new InputEvent("change"));
    }
  });

  document.querySelectorAll('[name="share_customize_services"]').forEach((elem) => {
    elem.addEventListener("change", () => {
      shareCustomizeServicesWrapper.classList.toggle("d-none", Number(elem.value) === 0);
    });

    if (elem.checked) {
      elem.dispatchEvent(new InputEvent("change"));
    }
  });
})(document);
