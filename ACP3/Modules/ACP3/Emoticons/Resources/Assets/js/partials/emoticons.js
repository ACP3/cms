import { insertEmoticon } from "../libs/insert-emoticon";

/**
 * @type {HTMLTextAreaElement}
 */
const textarea = document.querySelector(document.querySelector(".icons").dataset.emoticonsInput);
document.querySelectorAll(".icons a").forEach((emoticonElem) => {
  insertEmoticon(emoticonElem, textarea);
});
