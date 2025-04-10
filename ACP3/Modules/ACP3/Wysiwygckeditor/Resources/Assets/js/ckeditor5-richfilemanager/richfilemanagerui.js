import { IconBrowseFiles, Plugin, ButtonView, MenuBarMenuListItemButtonView, IconImageAssetManager } from "ckeditor5";

export default class RichFilemanagerUI extends Plugin {
  /**
   * @inheritDoc
   */
  static get pluginName() {
    return "RichFilemanagerUI";
  }

  /**
   * @inheritDoc
   */
  init() {
    const editor = this.editor;
    editor.ui.componentFactory.add("richfilemanager", () => this.#createFileToolbarButton());
    editor.ui.componentFactory.add("menuBar:richfilemanager", () => this.#createFileMenuBarButton());
    if (editor.plugins.has("ImageInsertUI")) {
      editor.plugins.get("ImageInsertUI").registerIntegration({
        name: "assetManager",
        observable: () => editor.commands.get("richfilemanager"),
        buttonViewCreator: () => this.#createImageToolbarButton(),
        formViewCreator: () => this.#createImageDropdownButton(),
        menuBarButtonViewCreator: (isOnly) => this.#createImageMenuBarButton(isOnly ? "insertOnly" : "insertNested"),
      });
    }
  }

  #createButton(ButtonClass) {
    const editor = this.editor;
    const locale = editor.locale;
    const view = new ButtonClass(locale);
    const command = editor.commands.get("richfilemanager");
    view.bind("isEnabled").to(command);
    view.on("execute", () => {
      editor.execute("richfilemanager");
      editor.editing.view.focus();
    });
    return view;
  }

  #createFileToolbarButton() {
    const t = this.editor.locale.t;
    const button = this.#createButton(ButtonView);
    button.icon = IconBrowseFiles;
    button.label = t("Insert image or file");
    button.tooltip = true;
    return button;
  }

  #createImageToolbarButton() {
    const t = this.editor.locale.t;
    const imageInsertUI = this.editor.plugins.get("ImageInsertUI");
    const button = this.#createButton(ButtonView);
    button.icon = IconImageAssetManager;
    button
      .bind("label")
      .to(imageInsertUI, "isImageSelected", (isImageSelected) =>
        isImageSelected ? t("Replace image with file manager") : t("Insert image with file manager"),
      );
    button.tooltip = true;
    return button;
  }

  #createImageDropdownButton() {
    const t = this.editor.locale.t;
    const imageInsertUI = this.editor.plugins.get("ImageInsertUI");
    const button = this.#createButton(ButtonView);
    button.icon = IconImageAssetManager;
    button.withText = true;
    button
      .bind("label")
      .to(imageInsertUI, "isImageSelected", (isImageSelected) =>
        isImageSelected ? t("Replace with file manager") : t("Insert with file manager"),
      );
    button.on("execute", () => {
      imageInsertUI.dropdownView.isOpen = false;
    });
    return button;
  }

  #createFileMenuBarButton() {
    const t = this.editor.locale.t;
    const button = this.#createButton(MenuBarMenuListItemButtonView);
    button.icon = IconBrowseFiles;
    button.withText = true;
    button.label = t("File");
    return button;
  }

  #createImageMenuBarButton(type) {
    const translateVariableKey = this.editor.locale.t;
    const t = this.editor.locale.t;
    const button = this.#createButton(MenuBarMenuListItemButtonView);
    button.icon = IconImageAssetManager;
    button.withText = true;
    switch (type) {
      case "insertOnly":
        button.label = translateVariableKey("Image");
        break;
      case "insertNested":
        button.label = t("With file manager");
        break;
    }
    return button;
  }
}
