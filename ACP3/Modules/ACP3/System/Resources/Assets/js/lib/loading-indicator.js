/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

export class LoadingIndicator {
  /**
   *
   * @param {HTMLElement} targetElement
   */
  addLoadingIndicator(targetElement) {
    if (!targetElement.querySelector(".loading-indicator")) {
      const body = document.querySelector("body");
      const jsSvgIcons = JSON.parse(body.dataset.svgIcons);
      const loadingIndicatorHtml =
        '<svg class="svg-icon svg-icon__spinner svg-icon--spin loading-indicator" fill="currentColor"><use xlink:href="' +
        jsSvgIcons["loadingIndicatorIcon"] +
        '"></use></svg>';

      targetElement.insertAdjacentHTML("afterbegin", loadingIndicatorHtml);
    }
  }

  /**
   *
   * @param {HTMLElement} targetElement
   */
  showLoadingIndicator(targetElement) {
    this.#toggleLoadingIndicator(targetElement, true);
  }

  /**
   *
   * @param {HTMLElement} targetElement
   */
  hideLoadingIndicator(targetElement) {
    this.#toggleLoadingIndicator(targetElement, false);
  }

  /**
   *
   * @param {HTMLElement} targetElement
   * @param {Boolean} show
   */
  #toggleLoadingIndicator(targetElement, show) {
    targetElement.querySelector(".loading-indicator")?.classList.toggle("loading-indicator__active", show);
  }
}
