/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

/**
 *
 * @param {Element} emoticonElement
 * @param {HTMLTextAreaElement} textarea
 */
export function insertEmoticon(emoticonElement, textarea) {
  let caretPosition = 0;

  emoticonElement.addEventListener("click", (event) => {
    event.preventDefault();

    const currentValue = textarea.value;
    const textBeforeSelection = currentValue.substr(0, caretPosition);
    const textAfterSelection = currentValue.substr(caretPosition);

    // Add leading and trailing spaces to the to be inserted emoticon, so that we aren't getting into overflow problems, etc.
    const emoticonCode = ` ${emoticonElement.title} `;

    caretPosition += emoticonCode.length;

    textarea.value = textBeforeSelection + emoticonCode + textAfterSelection;
    textarea.focus();

    textarea.setSelectionRange(caretPosition, caretPosition);
  });

  // When clicking on one of the emoticons, the textarea's blur event gets triggered.
  // We can use this to save the last known caret position, so that the emoticon can be inserted into the correct
  // position
  textarea.addEventListener("blur", () => {
    caretPosition = textarea.selectionStart;
  });
}
