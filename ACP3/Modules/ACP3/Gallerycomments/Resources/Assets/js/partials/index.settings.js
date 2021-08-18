((document) => {
  const overlayFormFields = document.querySelectorAll('[name="overlay"]');

  overlayFormFields.forEach((elem) => {
    elem.addEventListener("change", () => {
      document.getElementById("comments-container").classList.toggle("d-none", Number(elem.value) !== 0);
    });

    if (elem.checked) {
      elem.dispatchEvent(new InputEvent("change"));
    }
  });
})(document);
