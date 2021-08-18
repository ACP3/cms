/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

/* global onloadCallback:true */
onloadCallback = () => {
  document.querySelectorAll(".recaptcha-placeholder").forEach((elem) => {
    if (elem.children.length === 0) {
      elem.dataset.recaptchaId = grecaptcha.render(elem.id, {
        sitekey: elem.dataset.sitekey,
        size: elem.dataset.size,
      });
    }
  });
};

document.addEventListener("acp3.ajaxFrom.complete", () => {
  onloadCallback();
});

document.addEventListener("acp3.ajaxFrom.submit.fail", (event, ajaxForm) => {
  const reCaptchaPlaceholder = ajaxForm.element.querySelector(".recaptcha-placeholder");

  if (reCaptchaPlaceholder?.length > 0) {
    grecaptcha.reset(reCaptchaPlaceholder.dataset.recaptchaId);
  }
});
