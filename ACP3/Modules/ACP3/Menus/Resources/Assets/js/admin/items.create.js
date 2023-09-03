((document) => {
  const moduleContainer = document.getElementById("module-container");
  const linkContainer = document.getElementById("link-container");
  const hints = linkContainer.querySelector(".form-text");
  const targetContainer = document.getElementById("target-container");

  const PAGE_TYPE_MODULE = 1;
  const PAGE_TYPE_DYNAMIC_PAGE = 2;
  const PAGE_TYPE_HEADLINE = 4;

  // Wenn Menüpunkt nicht angezeigt werden soll, Linkziel verstecken
  document.querySelectorAll('[name="display"]').forEach((elem) => {
    elem.addEventListener("change", () => {
      const value = Number(elem.value);
      targetContainer.classList.toggle("d-none", value === 0);

      if (value === 0) {
        // Force the link target to open on the same page programmatically,
        // as the user can not select it for them self
        document.getElementById("target").value = 1;
      }
    });
  });

  const pageTypeFormField = document.getElementById("mode");
  let currentPageType = pageTypeFormField.value;

  // Seitentyp
  pageTypeFormField.addEventListener("change", () => {
    const pageType = Number(pageTypeFormField.value);

    moduleContainer.classList.toggle("d-none", pageType !== PAGE_TYPE_MODULE);
    hints.classList.toggle("d-none", pageType !== PAGE_TYPE_DYNAMIC_PAGE);
    linkContainer.classList.toggle("d-none", pageType === PAGE_TYPE_MODULE || pageType === PAGE_TYPE_HEADLINE);
    targetContainer.classList.toggle("d-none", pageType === PAGE_TYPE_HEADLINE);

    if (pageType === PAGE_TYPE_MODULE) {
      // Modul im Dropdown-Menü selektieren, falls zuvor als Modus eine dynamische Seite aktiv war
      if (currentPageType === PAGE_TYPE_DYNAMIC_PAGE) {
        const uriFormField = document.getElementById("uri");
        const match = uriFormField.value.match(/^([a-z\d_-]+)\/([a-z\d_-]+\/)+$/);

        if (
          match[1] != null &&
          document.getElementById("module").querySelector('option[value="' + match[1] + '"]').length > 0
        ) {
          document.getElementById("link-module").value = match[1];
        }
      }
    }

    currentPageType = pageType;
  });

  pageTypeFormField.dispatchEvent(new InputEvent("change"));
})(document);
