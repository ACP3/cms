((document) => {
  const fileTypeToggle = document.querySelector('input[name="external"]');

  fileTypeToggle.addEventListener("click", function (event) {
    if (event.detail?.init === true) {
      event.preventDefault();
    }

    document.getElementById("file-external-toggle").classList.toggle("hidden", !this.checked);
    document.getElementById("file-internal-toggle").classList.toggle("hidden", this.checked);
  });

  fileTypeToggle.dispatchEvent(new CustomEvent("click", { detail: { init: true } }));
})(document);
