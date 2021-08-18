((document) => {
  const readmoreWrapper = document.getElementById("readmore-characters-wrapper");

  document.querySelectorAll('[name="readmore"]').forEach((elem) => {
    elem.addEventListener("change", () => {
      readmoreWrapper.classList.toggle("d-none", Number(elem.value) === 0);
    });

    if (elem.checked) {
      elem.dispatchEvent(new InputEvent("change"));
    }
  });
})(document);
