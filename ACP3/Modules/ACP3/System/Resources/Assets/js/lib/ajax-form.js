/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

import { mergeSettings } from "./utils";

export class AjaxForm {
  #element;
  #loadingLayer;
  #formValidator;
  #defaults = {
    completeCallback: null,
    targetElement: "#content",
    loadingOverlay: true,
    customFormData: null,
    method: null,
  };
  #settings;

  /**
   *
   * @param {HTMLElement} element
   * @param {LoadingLayer} loadingLayer
   * @param {FormValidator} formValidator
   * @param {object} options
   */
  constructor(element, loadingLayer, formValidator, options = {}) {
    this.#element = element;
    this.isFormValid = true;

    this.#loadingLayer = loadingLayer;
    this.#formValidator = formValidator;

    this.#settings = mergeSettings(this.#defaults, options, element.dataset);
    this.#init();
  }

  #init() {
    this.#findSubmitButton();
    this.#addLoadingLayer();

    if (this.#element.nodeName === "A") {
      this.#element.addEventListener("click", async () => {
        await this.performAjaxRequest();
      });
    } else {
      this.#element.noValidate = true;

      this.#element.addEventListener("submit", async (e) => {
        e.preventDefault();

        this.#formValidator.setFormAsValid();

        document.dispatchEvent(new CustomEvent("acp3.ajaxFrom.submit.before", { detail: this }));

        if (this.#formValidator.isFormValid && this.#formValidator.preValidateForm()) {
          await this.performAjaxRequest();
        }
      });

      this.#element.addEventListener("change", () => {
        if (this.#formValidator.isFormValid === false) {
          this.#formValidator.checkFormElementsForErrors();
        }
      });
    }
  }

  #findSubmitButton() {
    this.#element.querySelectorAll("[type=submit]").forEach((submitElem) => {
      submitElem.addEventListener("click", () => {
        this.#element.querySelectorAll("[type=submit]").forEach((elem) => {
          delete elem.dataset["clicked"];
        });
        submitElem.dataset.clicked = "true";
      });
    });
  }

  async performAjaxRequest() {
    const form = this.#element;

    let hash, submitButton;

    if (this.#element.getAttribute("method")) {
      submitButton = this.#element.querySelector('[type="submit"][data-clicked="true"]');

      hash = submitButton?.dataset.hashChange;
    } else {
      hash = form.dataset.hashChange;
    }

    this.#loadingLayer.showLoadingLayer();
    this.#disableSubmitButton(submitButton);

    try {
      const method = form.getAttribute("method")?.toUpperCase() ?? this.#settings.method?.toUpperCase() ?? "GET";

      const response = await fetch(form.getAttribute("action") || form.getAttribute("href"), {
        method: method,
        body: method !== "GET" ? this.#prepareFormData(submitButton) : null,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      if (!response.ok) {
        await this.#handleResponseError(response);
      } else {
        await this.#handleSuccessfulResponse(response, hash);
      }
    } catch (error) {
      console.error(error);
    } finally {
      this.#loadingLayer.hideLoadingLayer();
      this.#enableSubmitButton(submitButton);
    }
  }

  /**
   *
   * @returns {FormData}
   */
  #prepareFormData(submitButton) {
    const form = this.#element;

    const initialData = this.#settings.customFormData || {};
    let data;

    if (form.getAttribute("method")) {
      data = new FormData(form);

      if (submitButton) {
        data.append(submitButton.getAttribute("name"), "1");
      }
    } else {
      data = new FormData();
    }

    for (let [key, value] of Object.entries(initialData)) {
      data.append(key, value);
    }

    return data;
  }

  /**
   *
   * @param {Response} response
   * @returns {Promise<void>}
   */
  async #handleResponseError(response) {
    const responseData = await response.clone().text();

    if (response.status === 400) {
      this.#formValidator.handleFormErrorMessages(this.#element, responseData);

      document.dispatchEvent(new CustomEvent("acp3.ajaxFrom.submit.fail", { detail: this }));
    } else if (responseData.length > 0) {
      document.open();
      document.write(responseData);
      document.close();
    }
  }

  /**
   *
   * @param {Response} response
   * @param {string} hash
   * @returns {Promise<void>}
   */
  async #handleSuccessfulResponse(response, hash) {
    const responseData = await this.#decodeResponse(response);

    if (typeof window[this.#settings.completeCallback] === "function") {
      window[this.#settings.completeCallback](responseData);
    } else if (responseData.redirect_url) {
      this.#redirectToNewPage(hash, responseData);
    } else {
      this.#replaceContent(hash, responseData);
      this.#scrollIntoView(hash);

      if (hash !== undefined) {
        window.location.hash = hash;
      }

      document.dispatchEvent(new CustomEvent("acp3.ajaxFrom.complete"));
    }
  }

  #addLoadingLayer() {
    if (this.#settings.loadingOverlay === false) {
      return;
    }

    this.#loadingLayer.addLoadingLayer();
  }

  /**
   *
   * @param {Element} submitButton
   */
  #disableSubmitButton(submitButton) {
    if (submitButton) {
      submitButton.disabled = true;
    }
  }

  /**
   *
   * @param {Element} submitButton
   */
  #enableSubmitButton(submitButton) {
    if (submitButton) {
      submitButton.disabled = false;
    }
  }

  /**
   *
   * @param {Response} response
   * @returns {Promise<*>}
   */
  async #decodeResponse(response) {
    try {
      return await response.clone().json();
    } catch (error) {
      return await response.clone().text();
    }
  }

  #redirectToNewPage(hash, responseData) {
    if (hash !== undefined) {
      window.location.href = responseData.redirect_url + hash;
      window.location.reload();
    } else {
      window.location.href = responseData.redirect_url;
    }
  }

  #scrollIntoView(hash) {
    setTimeout(() => {
      if (hash) {
        const targetElement = document.querySelector(`[data-hash-change="${hash}"]`);

        window.scrollTo({ top: targetElement.getBoundingClientRect().y, behavior: "smooth" });
      } else {
        const targetElement = document.querySelector(this.#settings.targetElement);
        const offsetTop = targetElement.getBoundingClientRect().y;

        if (document.scrollTop > offsetTop) {
          window.scrollTo({ top: offsetTop, behavior: "smooth" });
        }
      }
    });
  }

  #replaceContent(hash, responseData) {
    if (hash && document.querySelector(hash)) {
      const parser = new DOMParser();
      const doc = parser.parseFromString(responseData, "text/html");

      document.querySelector(hash).innerHTML = doc.querySelector(hash).innerHTML;
    } else {
      document.querySelector(this.#settings.targetElement).innerHTML = responseData;
    }
  }
}
