import { Plugin } from "ckeditor5";
import RichFilemanagerUI from "./richfilemanagerui";
import RichFilemanagerEditing from "./richfilemanagerediting";

export default class Richfilemanager extends Plugin {
  static get pluginName() {
    return "Richfilemanager";
  }

  static get requires() {
    return ["Link", "RichFilemanagerUploadAdapter", RichFilemanagerEditing, RichFilemanagerUI];
  }
}
