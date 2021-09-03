((document) => {
  const manageMenuItemContainer = document.getElementById("manage-menu-item-container");
  const parentIdFormField = document.getElementById("parent-id");
  const blockIdFormField = document.getElementById("block-id");

  const createMenuItemFormField = document.querySelector('[name="create_menu_item"]');

  if (createMenuItemFormField) {
    createMenuItemFormField.addEventListener("click", (event) => {
      if (event.detail?.init === true) {
        event.preventDefault();
      }

      manageMenuItemContainer.classList.toggle("d-none", !createMenuItemFormField.checked);
    });
    createMenuItemFormField.dispatchEvent(new CustomEvent("click", { detail: { init: true } }));
  }

  blockIdFormField.addEventListener("change", () => {
    const blockName = blockIdFormField.options.item(blockIdFormField.selectedIndex).text;

    const optionGroups = parentIdFormField.querySelectorAll("optgroup");

    optionGroups.forEach((optGroup) => {
      const optGroupEnabled = optGroup.getAttribute("label") === blockName;
      optGroup.disabled = !optGroupEnabled;
      optGroup.classList.toggle("d-none", !optGroupEnabled);

      if (!optGroupEnabled) {
        optGroup.querySelectorAll("option").forEach((option) => {
          option.selected = false;
        });
      }
    });
  });

  blockIdFormField.dispatchEvent(new InputEvent("change"));
})(document);
