/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

((document) => {
  const sitemapWrapper = document.getElementById("seo-sitemap-wrapper");

  document.querySelectorAll('[name="sitemap_is_enabled"]').forEach((elem) => {
    elem.addEventListener("change", () => {
      sitemapWrapper.classList.toggle("d-none", Number(elem.value) === 0);
    });

    if (elem.checked) {
      elem.dispatchEvent(new InputEvent("change"));
    }
  });
})(document);
