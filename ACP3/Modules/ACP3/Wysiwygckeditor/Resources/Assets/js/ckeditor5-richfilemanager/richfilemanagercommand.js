import { Command } from "ckeditor5";

let editorInstance;

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

    editorInstance = this.editor;

    const width = screen.width * 0.7;
    const height = screen.height * 0.7;
    const left = (screen.width - width) / 2;
    const top = (screen.height - height) / 2;
    let windowOptions = "toolbar=no,status=no,resizable=yes,dependent=yes";
    windowOptions += ",width=" + width;
    windowOptions += ",height=" + height;
    windowOptions += ",left=" + left;
    windowOptions += ",top=" + top;

    window.open(options.url, "richfilemanager-popup", windowOptions);
  }
}

window.SetUrl = (url) => {
  const imageCommand = editorInstance.commands.get("insertImage");
  // Check if inserting an image is actually possible - it might be possible to only insert a link.
  if (!imageCommand.isEnabled) {
    const notification = editorInstance.plugins.get("Notification");
    const t = editorInstance.locale.t;
    notification.showWarning(t("Could not insert image at the current position."), {
      title: t("Inserting image failed"),
      namespace: "richfilemanager",
    });
    return;
  }
  editorInstance.execute("insertImage", { source: url });
};
