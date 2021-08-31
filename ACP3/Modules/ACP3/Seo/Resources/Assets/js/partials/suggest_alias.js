/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

import { SuggestAlias } from "../lib/suggest-alias";

((document) => {
  new SuggestAlias(document.getElementById("seo-alias-suggestion"), {
    prefix: document.querySelector("[data-seo-slug-prefix]")?.dataset.seoSlugPrefix ?? "",
    slugBaseElement: document.querySelector('[data-seo-slug-base="true"]'),
    aliasElement: document.getElementById("alias"),
  });
})(document);
