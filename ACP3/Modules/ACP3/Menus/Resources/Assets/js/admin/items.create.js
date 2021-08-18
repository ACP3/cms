((document) => {
  const moduleContainer = document.getElementById("module-container");
  const linkContainer = document.getElementById("link-container");
  const hints = linkContainer.querySelector(".form-text");
  const targetContainer = document.getElementById("target-container");

  // Wenn Menüpunkt nicht angezeigt werden soll, Linkziel verstecken
  document.querySelectorAll('[name="display"]').forEach((elem) => {
    elem.addEventListener("change", () => {
      const value = Number(elem.value);
      targetContainer.classList.toggle("d-none", value === 1);

      if (value === 0) {
        // Force the link target to open on the same page programmatically,
        // as the user can not select it for them self
        document.getElementById("target").value = 1;
      }
    });
  });

  const modeFormField = document.getElementById("mode");
  let currentMode = modeFormField.value;

  // Seitentyp
  modeFormField.addEventListener("change", () => {
    const mode = Number(modeFormField.value);

    if (mode === 1) {
      moduleContainer.classList.remove("d-none");
      hints.classList.add("d-none");
      linkContainer.classList.add("d-none");

      // Modul im Dropdown-Menü selektieren, falls zuvor als Modus eine dynamische Seite aktiv war
      if (currentMode === 2) {
        const uriFormField = document.getElementById("uri");
        const match = uriFormField.value.match(/^([a-z\d_-]+)\/([a-z\d_-]+\/)+$/);

        if (
          match[1] != null &&
          document.getElementById("module").querySelector('option[value="' + match[1] + '"]').length > 0
        ) {
          document.getElementById("link-module").value = match[1];
        }
      }
    } else if (mode === 2) {
      moduleContainer.classList.add("d-none");
      hints.classList.remove("d-none");
      linkContainer.classList.remove("d-none");
    } else if (mode === 3) {
      moduleContainer.classList.add("d-none");
      hints.classList.add("d-none");
      linkContainer.classList.remove("d-none");
    }

    currentMode = mode;
  });

  modeFormField.dispatchEvent(new InputEvent("change"));
})(document);
