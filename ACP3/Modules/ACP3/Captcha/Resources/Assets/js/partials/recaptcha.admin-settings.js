/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

((document) => {
  const recaptchaWrapper = document.getElementById("recaptcha-wrapper"),
    captchaTypes = document.querySelector('select[name="captcha"]'),
    serviceIds = ["captcha.extension.recaptcha_captcha_extension"];

  captchaTypes.addEventListener("change", function () {
    recaptchaWrapper.classList.toggle("d-none", !serviceIds.includes(this.value));
  });
  captchaTypes.dispatchEvent(new InputEvent("change"));
})(document);
