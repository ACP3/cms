/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

(($, document) => {
  $(':radio[name="sitemap_is_enabled"]')
    .on("click change", function () {
      document.getElementById("seo-sitemap-wrapper").classList.toggle("hidden", Number(this.value) === 0);
    })
    .filter(":checked")
    .triggerHandler("click");
})(jQuery, document);
