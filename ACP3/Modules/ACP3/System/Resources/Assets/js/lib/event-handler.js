/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

/**
 *
 * @param {HTMLElement|Document} element
 * @param {string} eventName
 * @param {string|HTMLElement} selectorOrTargetElement
 * @param {function} handler
 */
export function delegateEvent(element, eventName, selectorOrTargetElement, handler) {
  element.addEventListener(eventName, (event) => {
    if (typeof selectorOrTargetElement === "string") {
      const elems = document.querySelectorAll(selectorOrTargetElement);

      if (!elems) {
        return;
      }

      elems.forEach((elem) => {
        if (elem && elem.contains(event.target)) {
          handler(event, elem);
        }
      });
    } else if (selectorOrTargetElement && selectorOrTargetElement.contains(event.target)) {
      handler(event, selectorOrTargetElement);
    }
  });
}
