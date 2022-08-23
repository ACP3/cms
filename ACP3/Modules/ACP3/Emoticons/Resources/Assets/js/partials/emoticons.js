import { insertEmoticon } from "../libs/insert-emoticon";

((document) => {
  document.querySelectorAll("[data-emoticons-input]").forEach((elem) => {
    /**
     * @type {HTMLTextAreaElement}
     */
    const textarea = document.querySelector(elem.dataset.emoticonsInput);

    elem.querySelectorAll("a").forEach((emoticonElem) => {
      insertEmoticon(emoticonElem, textarea);
    });
  });
})(document);
