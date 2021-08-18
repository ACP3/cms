/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

((document) => {
  document.querySelectorAll('input[name="active"]').forEach((elem) => {
    elem.addEventListener("click", () => {
      document.getElementById("publication-period-wrapper").classList.toggle("d-none", Number(elem.value) === 0);
    });
  });

  document.querySelector('input[name="active"]:checked').dispatchEvent(new MouseEvent("click"));
})(document);
