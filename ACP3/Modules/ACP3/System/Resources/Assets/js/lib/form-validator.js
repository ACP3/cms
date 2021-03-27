/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

import { mergeSettings } from "./utils";

export class FormValidator {
  #isFormValid = true;
  #defaults = {
    scrollOffsetElement: null,
  };
  #settings;
  #formElement;

  constructor(formElement, options) {
    this.#settings = mergeSettings(this.#defaults, options, jQuery(formElement).data());
    this.#formElement = formElement;
  }

  preValidateForm() {
    this.checkFormElementsForErrors();
    this.#focusTabWithFirstErrorMessage();
    this.#scrollToFirstFormError();

    return this.#isFormValid;
  }

  checkFormElementsForErrors() {
    this.#removeAllPreviousErrors();

    for (const field of this.#formElement.elements) {
      if (field.nodeName !== "INPUT" && field.nodeName !== "TEXTAREA" && field.nodeName !== "SELECT") {
        continue;
      }

      if (!field.checkValidity()) {
        this.#addErrorDecorationToFormGroup(field);
        this.#addErrorMessageToFormField(jQuery(field), field.validationMessage);

        this.#isFormValid = false;
      }
    }
  }

  #removeAllPreviousErrors() {
    const $form = jQuery(this.#formElement);

    $form.find(".form-group.has-error").removeClass("has-error");
    $form.find(".validation-failed").remove();
  }

  /**
   *
   * @param {HTMLElement} elem
   */
  #addErrorDecorationToFormGroup(elem) {
    elem.closest(".form-group")?.classList.add("has-error");
  }

  /**
   *
   * @param {HTMLElement} elem
   */
  #removeErrorMessageFromFormField(elem) {
    const elementContainingValidationErrors = elem.closest("div")?.querySelector(".validation-failed");

    elementContainingValidationErrors?.parentElement.removeChild(elementContainingValidationErrors);
  }

  #addErrorMessageToFormField($element, errorMessage) {
    this.#removeErrorMessageFromFormField($element[0]);

    const $body = jQuery("body");
    const jsSvgIcons = $body.data("svgIcons");

    $element
      .closest("div:not(.input-group):not(.btn-group)")
      .append(
        `<small class="help-block validation-failed"><svg class="svg-icon svg-icon__exclamation-circle" fill="currentColor"><use xlink:href="${jsSvgIcons["validationFailedIcon"]}"></use></svg> ${errorMessage}</small>`
      );
  }

  #focusTabWithFirstErrorMessage() {
    if (jQuery(".tabbable").length > 0) {
      const $elem = jQuery(".tabbable .form-group.has-error:first");
      const tabId = $elem.closest(".tab-pane").prop("id");

      jQuery('.tabbable .nav-tabs a[href="#' + tabId + '"]').tab("show");

      $elem.find(":input").focus();
    }
  }

  get isFormValid() {
    return this.#isFormValid;
  }

  setFormAsValid() {
    this.#isFormValid = true;
  }

  handleFormErrorMessages($form, errorMessagesHtml) {
    let $errorBox = jQuery("#error-box");
    const $modalBody = $form.find(".modal-body");

    $errorBox.remove();

    // Place the error messages inside the modal body for a better styling
    $errorBox = jQuery(errorMessagesHtml);

    $errorBox
      .hide()
      .prependTo($modalBody.length > 0 && $modalBody.is(":visible") ? $modalBody : $form)
      .fadeIn();

    this.#prettyPrintResponseErrorMessages($form, $errorBox);
  }

  #prettyPrintResponseErrorMessages($form, $errorBox) {
    this.#removeAllPreviousErrors();

    // highlight all input fields where the validation has failed
    $errorBox.find("li").each((index, element) => {
      let $this = jQuery(element),
        errorClass = $this.data("error");

      if (errorClass.length > 0) {
        let $elem =
          $form.find("#" + errorClass) || $form.find('[id|="' + errorClass + '"]').filter(':not([id$="container"])');

        if ($elem.length > 0) {
          this.#addErrorDecorationToFormGroup($elem[0]);

          // Move the error message to the responsible input field(s)
          // and remove the list item from the error box container
          if ($elem.length === 1) {
            this.#addErrorMessageToFormField($elem, $this.html());
            $this.remove();
          }
        }
      }
    });

    // if all list items have been removed, remove the error box container too
    if ($errorBox.find("li").length === 0) {
      $errorBox.remove();
    }

    this.#focusTabWithFirstErrorMessage();
    this.#scrollToFirstFormError();
  }

  #scrollToFirstFormError() {
    const $form = jQuery(this.#formElement);
    const $formErrors = $form.find(".form-group.has-error");

    if ($form.closest(".modal").length > 0) {
      return;
    }

    if (!$formErrors || $formErrors.length === 0) {
      return;
    }

    if (this.#isElementInViewport($form.find(".help-block.validation-failed"))) {
      return;
    }

    let offsetTop = $formErrors.offset().top;

    if (this.#settings.scrollOffsetElement) {
      const $scrollOffsetElement = jQuery(this.#settings.scrollOffsetElement);

      if ($scrollOffsetElement && $scrollOffsetElement.length > 0) {
        offsetTop -= $scrollOffsetElement.height();
      }
    }

    window.scrollTo({ top: offsetTop, behavior: "smooth" });
  }

  #isElementInViewport(element) {
    // special bonus for those using jQuery
    if (typeof jQuery === "function" && element instanceof jQuery) {
      element = element[0];
    }

    const $scrollOffsetElement = jQuery(this.#settings.scrollOffsetElement);
    let offsetTop = 0;

    if ($scrollOffsetElement) {
      offsetTop = $scrollOffsetElement.height();
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
