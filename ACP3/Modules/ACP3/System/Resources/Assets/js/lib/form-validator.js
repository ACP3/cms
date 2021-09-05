/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

import { mergeSettings } from "./utils";

export class FormValidator {
  #defaults = {
    scrollOffsetElement: null,
  };
  #settings;

  constructor(options = {}) {
    this.#settings = mergeSettings(this.#defaults, options, {});
  }

  /**
   *
   * @param {HTMLFormElement} formElement
   * @returns {boolean}
   */
  preValidateForm(formElement) {
    this.checkFormElementsForErrors(formElement);
    this.#focusTabWithFirstErrorMessage(formElement);
    this.#scrollToFirstFormError(formElement);

    return this.isValid(formElement);
  }

  /**
   *
   * @param {HTMLFormElement} formElement
   */
  checkFormElementsForErrors(formElement) {
    this.#removeAllPreviousErrors(formElement);

    for (const field of formElement.elements) {
      if (field.nodeName !== "INPUT" && field.nodeName !== "TEXTAREA" && field.nodeName !== "SELECT") {
        continue;
      }

      if (!field.checkValidity()) {
        this.#addErrorMessageToFormField(field, field.validationMessage);

        formElement.formValidator.isValid = false;
      }
    }
  }

  /**
   * @param {HTMLFormElement} formElement
   */
  #removeAllPreviousErrors(formElement) {
    formElement.querySelectorAll(".is-invalid").forEach((invalidFormField) => {
      invalidFormField.classList.remove("is-invalid");
    });
  }

  /**
   *
   * @param {HTMLElement} formField
   */
  #removeErrorMessageFromFormField(formField) {
    formField.closest("div")?.querySelector(".invalid-feedback")?.remove();
  }

  /**
   *
   * @param {HTMLElement} formField
   * @param {string} errorMessage
   */
  #addErrorMessageToFormField(formField, errorMessage) {
    this.#removeErrorMessageFromFormField(formField);

    formField.classList.add("is-invalid");

    formField
      .closest("div:not(.input-group):not(.btn-group)")
      .insertAdjacentHTML("beforeend", `<div class="invalid-feedback">${errorMessage}</div>`);
  }

  /**
   *
   * @param {HTMLFormElement} formElement
   */
  #focusTabWithFirstErrorMessage(formElement) {
    if (!formElement.querySelector(".nav-tabs")) {
      return;
    }

    const firstElemWithError = formElement.querySelector(".is-invalid");

    if (!firstElemWithError) {
      return;
    }

    const tabId = firstElemWithError.closest(".tab-pane").getAttribute("id");

    formElement.querySelector('.nav-tabs a[href="#' + tabId + '"]').click();

    firstElemWithError.focus();
  }

  /**
   *
   * @param {HTMLFormElement} formElement
   * @returns {boolean}
   */
  isValid(formElement) {
    return !!formElement.formValidator?.isValid;
  }

  /**
   *
   * @param {HTMLFormElement} formElement
   */
  setFormAsValid(formElement) {
    formElement.formValidator = {
      isValid: true,
    };
  }

  /**
   *
   * @param {HTMLElement} targetElement
   * @param {string} errorMessagesHtml
   */
  handleFormErrorMessages(targetElement, errorMessagesHtml) {
    if (!(targetElement instanceof HTMLFormElement)) {
      return;
    }

    const modalBody = targetElement.querySelector(".modal-body");

    // Remove the old - possible existing - error-box
    document.getElementById("error-box")?.remove();

    // Place the error messages inside the modal body for a better styling
    if (modalBody) {
      modalBody.insertAdjacentHTML("afterbegin", errorMessagesHtml);
    } else {
      targetElement.insertAdjacentHTML("afterbegin", errorMessagesHtml);
    }

    this.#prettyPrintResponseErrorMessages(targetElement, document.getElementById("error-box"));
  }

  /**
   *
   * @param {HTMLFormElement} formElement
   * @param {HTMLElement} errorBox
   */
  #prettyPrintResponseErrorMessages(formElement, errorBox) {
    this.#removeAllPreviousErrors(formElement);

    // highlight all input fields where the validation has failed
    errorBox.querySelectorAll("li").forEach((errorMessageLine) => {
      let errorClass = errorMessageLine.dataset.element;

      if (errorClass.length > 0) {
        let elem = document.getElementById(errorClass) || formElement.querySelector('[id|="' + errorClass + '"]');

        if (elem) {
          // Move the error message to the responsible input field(s)
          // and remove the list item from the error box container
          this.#addErrorMessageToFormField(elem[0], errorMessageLine.innerHTML);
          errorMessageLine.remove();
        }
      }
    });

    // if all list items have been removed, remove the error box container too
    if (errorBox.querySelectorAll("li").length === 0) {
      errorBox.remove();
    }

    this.#focusTabWithFirstErrorMessage(formElement);
    this.#scrollToFirstFormError(formElement);
  }

  /**
   * @param {HTMLFormElement} formElement
   */
  #scrollToFirstFormError(formElement) {
    if (formElement.closest(".modal")?.length > 0) {
      return;
    }

    const formErrors = formElement.querySelectorAll(".is-invalid");

    if (!formErrors || formErrors.length === 0) {
      return;
    }

    if (this.#isElementInViewport(formElement.querySelector(".invalid-feedback"))) {
      return;
    }

    let offsetTop = formErrors.item(0).getBoundingClientRect().top;

    if (this.#settings.scrollOffsetElement) {
      const scrollOffsetElement = document.querySelector(this.#settings.scrollOffsetElement);

      if (scrollOffsetElement) {
        offsetTop -= scrollOffsetElement.clientHeight;
      }
    }

    window.scrollTo({ top: offsetTop, behavior: "smooth" });
  }

  #isElementInViewport(element) {
    const scrollOffsetElement = document.querySelector(this.#settings.scrollOffsetElement);
    let offsetTop = 0;

    if (scrollOffsetElement) {
      offsetTop = scrollOffsetElement.clientHeight;
    }

    const rect = element.getBoundingClientRect();

    return (
      rect.top >= offsetTop &&
      rect.left >= 0 &&
      rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
      rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
  }
}
