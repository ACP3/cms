/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

import { mergeSettings } from "./utils";

export class LoadingLayer {
  #options = {
    loadingText: "",
  };

  constructor(element, options) {
    this.#options = mergeSettings(this.#options, options, jQuery(element).data() || {});
  }

  addLoadingLayer() {
    if (!document.getElementById("loading-layer")) {
      const $body = jQuery("body");
      const jsSvgIcons = $body.data("svgIcons");
      const html =
        '<div id="loading-layer" class="loading-layer"><h1><svg class="svg-icon svg-icon__spinner svg-icon--spin" fill="currentColor"><use xlink:href="' +
        jsSvgIcons["loadingLayerIcon"] +
        '"></use></svg> ' +
        this.#options.loadingText +
        "</h1></div>";

      jQuery(html).appendTo($body);
    }
  }

  showLoadingLayer() {
    this.#toggleLoadingLayer(true);
  }

  hideLoadingLayer() {
    this.#toggleLoadingLayer(false);
  }

  #toggleLoadingLayer(show) {
    document.getElementById("loading-layer")?.classList.toggle("loading-layer__active", show);
  }
}
