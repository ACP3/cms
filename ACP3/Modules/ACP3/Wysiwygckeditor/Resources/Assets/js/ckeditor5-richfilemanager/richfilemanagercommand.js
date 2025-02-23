import { Command } from "ckeditor5";

export default class RichFilemanagerCommand extends Command {
  /**
   * @inheritDoc
   */
  constructor(editor) {
    super(editor);
    this.affectsData = false;
    // Remove default document listener to lower its priority.
    this.stopListening(this.editor.model.document, "change");
    // Lower this command listener priority to be sure that refresh() will be called after link & image refresh.
    this.listenTo(this.editor.model.document, "change", () => this.refresh(), { priority: "low" });
  }

  /**
   * @inheritDoc
   */
  refresh() {
    const imageCommand = this.editor.commands.get("insertImage");
    const linkCommand = this.editor.commands.get("link");
    this.isEnabled = imageCommand.isEnabled || linkCommand.isEnabled;
  }

  /**
   * @inheritDoc
   */
  execute() {
    const options = this.editor.config.get("richfilemanager.options") || {};

    window.open(options.url, "richfilemanager-popup", "popup=true");
  }
}

// function insertImages(editor, urls) {
//   const imageCommand = editor.commands.get("insertImage");
//   // Check if inserting an image is actually possible - it might be possible to only insert a link.
//   if (!imageCommand.isEnabled) {
//     const notification = editor.plugins.get("Notification");
//     const t = editor.locale.t;
//     notification.showWarning(t("Could not insert image at the current position."), {
//       title: t("Inserting image failed"),
//       namespace: "richfilemanager",
//     });
//     return;
//   }
//   editor.execute("insertImage", { source: urls });
// }
