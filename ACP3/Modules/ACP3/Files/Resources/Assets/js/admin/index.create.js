((document) => {
  const fileTypeToggle = document.querySelector('input[name="external"]');

  fileTypeToggle.addEventListener("click", function (event) {
    if (event.detail?.init === true) {
      event.preventDefault();
    }

    document.getElementById("file-external-toggle").classList.toggle("d-none", !this.checked);
    document.getElementById("file-internal-toggle").classList.toggle("d-none", this.checked);
  });

  fileTypeToggle.dispatchEvent(new CustomEvent("click", { detail: { init: true } }));
})(document);
