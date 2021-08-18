import { mergeSettings } from "../../../../../System/Resources/Assets/js/lib/utils";

export class SuggestAlias {
  #element;
  #settings;

  constructor(element, options) {
    this.#element = element;
    this.#settings = mergeSettings({
      prefix: "",
      slugBaseElement: null,
      aliasElement: null
    }, options);

    this.#init();
  }

  #init() {
    this.#element.addEventListener("click", async (e) => {
      e.preventDefault();

      await this.#performAjaxRequest();
    });
  }

  async #performAjaxRequest() {
    if (!this.#canPerformAjaxRequest()) {
      return;
    }

    try {
      this.#element.classList.add("disabled");

      const data = new FormData();
      data.append("prefix", this.#settings.prefix);
      data.append("title", this.#settings.slugBaseElement.value);

      const response = await fetch(this.#element.href, {
        method: "POST",
        body: data,
        headers: {
          "X-Requested-With": "XMLHttpRequest"
        }
      });

      if (response.ok) {
        const responseData = await response.json();

        if (responseData?.alias?.length > 0) {
          this.#settings.aliasElement.value = responseData.alias;
        }
      }
    } catch (err) {
      console.error(err.message);
    } finally {
      this.#element.classList.remove("disabled");
    }
  }

  /**
   * @returns {boolean}
   */
  #canPerformAjaxRequest() {
    return (
      this.#settings.slugBaseElement !== null &&
      this.#settings.aliasElement !== null &&
      this.#settings.slugBaseElement.value !== ""
    );
  }
}
