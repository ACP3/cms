/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

import { mergeSettings } from "./utils";

export class AjaxForm {
  #loadingLayer;
  #formValidator;
  #defaults = {
    targetElement: "#content",
    loadingOverlay: true,
    customFormData: null,
    scrollOffsetElement: null,
    method: null,
  };

  /**
   *
   * @param {HTMLElement} element
   * @param {LoadingLayer} loadingLayer
   * @param {FormValidator} formValidator
   * @param {object} options
   */
  constructor(element, loadingLayer, formValidator, options) {
    this.element = element;
    this.isFormValid = true;

    this.#loadingLayer = loadingLayer;
    this.#formValidator = formValidator;

    this.settings = mergeSettings(this.#defaults, options, jQuery(element).data());
    this.#init();
  }

  #init() {
    const that = this;

    this.findSubmitButton();
    this.addLoadingLayer();
    this.element.noValidate = true;

    jQuery(this.element)
      .on("submit", (e) => {
        e.preventDefault();

        this.#formValidator.setFormAsValid();

        jQuery(document).trigger("acp3.ajaxFrom.submit.before", [this]);

        if (this.#formValidator.isFormValid && this.#formValidator.preValidateForm(this.element)) {
          this.performAjaxRequest();
        }
      })
      .on("click", function (e) {
        if (jQuery(this).prop("tagName") === "A") {
          e.preventDefault();

          that.performAjaxRequest();
        }
      })
      .on("change", () => {
        if (this.#formValidator.isFormValid === false) {
          this.#formValidator.removeAllPreviousErrors();
          this.#formValidator.checkFormElementsForErrors(this.element);
        }
      });
  }

  findSubmitButton() {
    jQuery(this.element)
      .find(":submit")
      .click(function () {
        jQuery(":submit", jQuery(this).closest("form")).removeAttr("data-clicked");
        jQuery(this).attr("data-clicked", "true");
      });
  }

  async performAjaxRequest() {
    const $form = jQuery(this.element);

    let hash, $submitButton;

    if ($form.attr("method")) {
      $submitButton = jQuery(':submit[data-clicked="true"]', $form);

      hash = $submitButton.data("hashChange");
    } else {
      hash = $form.data("hashChange");
    }

    this.#loadingLayer.showLoadingLayer();
    this.disableSubmitButton($submitButton);

    try {
      const response = await fetch($form.attr("action") || $form.attr("href"), {
        method: $form.attr("method")
          ? $form.attr("method").toUpperCase()
          : this.settings.method?.toUpperCase() ?? "GET",
        body: this.prepareFormData($form),
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      if (!response.ok) {
        await this.handleResponseError(response, $form);
      } else {
        await this.handleSuccessfulResponse(response, $form, hash);
      }
    } catch (error) {
      console.error(error);
    } finally {
      this.#loadingLayer.hideLoadingLayer();
      this.enableSubmitButton($submitButton);
    }
  }

  /**
   *
   * @param {jQuery} $form
   * @returns {FormData}
   */
  prepareFormData($form) {
    const initialData = this.settings.customFormData || {};
    let data;

    if ($form.attr("method")) {
      const $submitButton = jQuery(':submit[data-clicked="true"]', $form);

      data = new FormData($form[0]);

      if ($submitButton.length) {
        data.append($submitButton.attr("name"), "1");
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
   * @param {jQuery} $form
   * @returns {Promise<void>}
   */
  async handleResponseError(response, $form) {
    const responseData = await response.clone().text();

    if (response.status === 400) {
      this.#formValidator.handleFormErrorMessages($form, responseData);

      jQuery(document).trigger("acp3.ajaxFrom.submit.fail", [this]);
    } else if (responseData.length > 0) {
      document.open();
      document.write(responseData);
      document.close();
    }
  }

  /**
   *
   * @param {Response} response
   * @param {jQuery} $form
   * @param {string} hash
   * @returns {Promise<void>}
   */
  async handleSuccessfulResponse(response, $form, hash) {
    const responseData = await this.decodeResponse(response);

    let callback = $form.data("ajax-form-complete-callback");

    if (typeof window[callback] === "function") {
      window[callback](responseData);
    } else if (responseData.redirect_url) {
      this.redirectToNewPage(hash, responseData);
    } else {
      this.scrollIntoView();
      this.replaceContent(hash, responseData);
      this.rebindHandlers(hash);

      jQuery(document).trigger("acp3.ajaxFrom.complete");

      if (typeof hash !== "undefined") {
        window.location.hash = hash;
      }
    }
  }

  addLoadingLayer() {
    if (this.settings.loadingOverlay === false) {
      return;
    }

    this.#loadingLayer.addLoadingLayer();
  }

  disableSubmitButton($submitButton) {
    if (typeof $submitButton !== "undefined") {
      $submitButton.prop("disabled", true);
    }
  }

  enableSubmitButton($submitButton) {
    if (typeof $submitButton !== "undefined") {
      $submitButton.prop("disabled", false);
    }
  }

  /**
   *
   * @param {Response} response
   * @returns {Promise<*>}
   */
  async decodeResponse(response) {
    try {
      return await response.clone().json();
    } catch (error) {
      return await response.clone().text();
    }
  }

  redirectToNewPage(hash, responseData) {
    if (typeof hash !== "undefined") {
      window.location.href = responseData.redirect_url + hash;
      window.location.reload();
    } else {
      window.location.href = responseData.redirect_url;
    }
  }

  /**
   * Scroll to the beginning of the content area, if the current viewport is near the bottom
   */
  scrollIntoView() {
    const offsetTop = jQuery(this.settings.targetElement).offset().top;

    if (jQuery(document).scrollTop() > offsetTop) {
      jQuery("html, body").animate(
        {
          scrollTop: offsetTop,
        },
        "fast"
      );
    }
  }

  replaceContent(hash, responseData) {
    if (hash && jQuery(hash).length) {
      jQuery(hash).html(jQuery(responseData).find(hash).html());
    } else {
      jQuery(this.settings.targetElement).html(responseData);
    }
  }

  rebindHandlers(hash) {
    const $bindingTarget = hash && jQuery(hash).length ? jQuery(hash) : jQuery(this.settings.targetElement);

    $bindingTarget.find('[data-ajax-form="true"]').formSubmit();

    this.findSubmitButton();
  }
}
