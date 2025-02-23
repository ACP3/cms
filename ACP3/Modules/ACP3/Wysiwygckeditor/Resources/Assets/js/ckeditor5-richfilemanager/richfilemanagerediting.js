import { Plugin, Notification, CKEditorError } from "ckeditor5";
import RichFilemanagerCommand from "./richfilemanagercommand";

export default class RichFilemanagerEditing extends Plugin {
  /**
   * @inheritDoc
   */
  static get pluginName() {
    return "RichFilemanagerEditing";
  }

  /**
   * @inheritDoc
   */
  static get requires() {
    return [Notification, "LinkEditing"];
  }

  /**
   * @inheritDoc
   */
  init() {
    const editor = this.editor;
    if (!editor.plugins.has("ImageBlockEditing") && !editor.plugins.has("ImageInlineEditing")) {
      throw new CKEditorError("richfilemanager-missing-image-plugin", editor);
    }
    editor.commands.add("richfilemanager", new RichFilemanagerCommand(editor));
  }
}
