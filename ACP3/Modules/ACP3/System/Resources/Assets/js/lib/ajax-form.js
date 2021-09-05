/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

import { mergeSettings } from "./utils";
import { delegateEvent } from "./event-handler";

export class AjaxForm {
  #loadingIndicator;
  #formValidator;
  #defaults = {
    completeCallback: null,
    targetElement: "#content",
    loadingOverlay: true,
    customFormData: null,
    method: null,
  };

  /**
   *
   * @param {LoadingIndicator} loadingIndicator
   * @param {FormValidator} formValidator
   */
  constructor(loadingIndicator, formValidator) {
    this.#loadingIndicator = loadingIndicator;
    this.#formValidator = formValidator;

    this.#init();
  }

  #init() {
    this.#registerClickedSubmitButton();

    delegateEvent(document, "click", 'a[data-ajax-form="true"]', async (event, elem) => {
      event.preventDefault();

      await this.performAjaxRequest(elem);
    });

    delegateEvent(document, "submit", 'form[data-ajax-form="true"]', async (event, elem) => {
      event.preventDefault();

      this.#formValidator.setFormAsValid(elem);

      document.dispatchEvent(new CustomEvent("acp3.ajaxFrom.submit.before", { detail: this }));

      if (this.#formValidator.isValid(elem) && this.#formValidator.preValidateForm(elem)) {
        await this.performAjaxRequest(elem);
      }
    });

    delegateEvent(document, "change", 'form[data-ajax-form="true"]', async (event, elem) => {
      if (this.#formValidator.isValid(elem) === false) {
        this.#formValidator.checkFormElementsForErrors(elem);
      }
    });
  }

  #registerClickedSubmitButton() {
    delegateEvent(document, "click", 'form[data-ajax-form="true"] [type="submit"]', (event, submitElem) => {
      submitElem
        .closest("form")
        .querySelectorAll("[type=submit]")
        .forEach((elem) => {
          delete elem.dataset["clicked"];
        });
      submitElem.dataset.clicked = "true";
    });
  }

  /**
   *
   * @param {HTMLElement} targetElement
   * @returns {Promise<void>}
   */
  async performAjaxRequest(targetElement) {
    const mergedSettings = mergeSettings(this.#defaults, {}, targetElement.dataset);
    let hash, submitButton;

    if (targetElement instanceof HTMLFormElement) {
      submitButton = targetElement.querySelector('[type="submit"][data-clicked="true"]');

      hash = submitButton?.dataset.hashChange;
    } else {
      hash = targetElement.dataset.hashChange;
    }

    if (mergedSettings.loadingOverlay) {
      this.#loadingIndicator.addLoadingIndicator(submitButton || targetElement);
      this.#loadingIndicator.showLoadingIndicator(submitButton || targetElement);
    }

    this.#disableSubmitButton(submitButton);

    try {
      const method =
        targetElement.getAttribute("method")?.toUpperCase() ?? mergedSettings.method?.toUpperCase() ?? "GET";

      const response = await fetch(targetElement.getAttribute("action") || targetElement.getAttribute("href"), {
        method: method,
        body: method !== "GET" ? this.#prepareFormData(targetElement, submitButton, mergedSettings) : null,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      if (!response.ok) {
        await this.#handleResponseError(response, targetElement);
      } else {
        await this.#handleSuccessfulResponse(response, hash, mergedSettings);
      }
    } catch (error) {
      console.error(error);
    } finally {
      this.#loadingIndicator.hideLoadingIndicator(submitButton || targetElement);
      this.#enableSubmitButton(submitButton);
    }
  }

  /**
   *
   * @param {HTMLFormElement} formElement
   * @param {HTMLElement} submitButton
   * @param {Record<string, any>} mergedSettings
   * @returns {FormData}
   */
  #prepareFormData(formElement, submitButton, mergedSettings) {
    const initialData = mergedSettings.customFormData || {};
    let data;

    if (formElement.getAttribute("method")) {
      data = new FormData(formElement);

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
   * @param {HTMLElement} targetElement
   * @returns {Promise<void>}
   */
  async #handleResponseError(response, targetElement) {
    const responseData = await response.clone().text();

    if (response.status === 400) {
      this.#formValidator.handleFormErrorMessages(targetElement, responseData);

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
   * @param {Record<string, any>} mergedSettings
   * @returns {Promise<void>}
   */
  async #handleSuccessfulResponse(response, hash, mergedSettings) {
    const responseData = await this.#decodeResponse(response);

    if (typeof window[mergedSettings.completeCallback] === "function") {
      window[mergedSettings.completeCallback](responseData);
    } else if (responseData.redirect_url) {
      this.#redirectToNewPage(hash, responseData);
    } else {
      this.#replaceContent(hash, responseData, mergedSettings);
      this.#scrollIntoView(hash, mergedSettings);

      if (hash !== undefined) {
        window.location.hash = hash;
      }

      document.dispatchEvent(new CustomEvent("acp3.ajaxFrom.complete"));
    }
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

  #scrollIntoView(hash, mergedSettings) {
    setTimeout(() => {
      if (hash) {
        const targetElement = document.querySelector(`[data-hash-change="${hash}"]`);

        window.scrollTo({ top: targetElement.getBoundingClientRect().y, behavior: "smooth" });
      } else {
        const targetElement = document.querySelector(mergedSettings.targetElement);
        const offsetTop = targetElement.getBoundingClientRect().y;

        if (document.scrollTop > offsetTop) {
          window.scrollTo({ top: offsetTop, behavior: "smooth" });
        }
      }
    });
  }

  #replaceContent(hash, responseData, mergedSettings) {
    if (hash && document.querySelector(hash)) {
      const parser = new DOMParser();
      const doc = parser.parseFromString(responseData, "text/html");

      document.querySelector(hash).innerHTML = doc.querySelector(hash).innerHTML;
    } else {
      document.querySelector(mergedSettings.targetElement).innerHTML = responseData;
    }
  }
}
