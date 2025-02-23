import { Plugin, FileRepository } from "ckeditor5";
import {} from "ckeditor5";

export default class RichFilemanagerUploadAdapter extends Plugin {
  /**
   * @inheritDoc
   */
  static get requires() {
    return [FileRepository];
  }

  /**
   * @inheritDoc
   */
  static get pluginName() {
    return "RichFilemanagerUploadAdapter";
  }

  /**
   * @inheritDoc
   */
  init() {
    const url = this.editor.config.get("richfilemanager.uploadUrl");
    if (!url) {
      return;
    }
    // Register CKFinderAdapter
    this.editor.plugins.get(FileRepository).createUploadAdapter = (loader) =>
      new UploadAdapter(loader, url, this.editor.t);
  }
}

/**
 * Upload adapter for RichFilemanager.
 */
class UploadAdapter {
  /**
   * Creates a new adapter instance.
   */
  constructor(loader, url, t) {
    this.loader = loader;
    this.url = url;
    this.t = t;
  }
  /**
   * Starts the upload process.
   *
   * @see module:upload/filerepository~UploadAdapter#upload
   */
  upload() {
    return this.loader.file.then((file) => {
      return new Promise((resolve, reject) => {
        this.#initRequest();
        this.#initListeners(resolve, reject, file);
        this.#sendRequest(file);
      });
    });
  }
  /**
   * Aborts the upload process.
   *
   * @see module:upload/filerepository~UploadAdapter#abort
   */
  abort() {
    if (this.xhr) {
      this.xhr.abort();
    }
  }
  /**
   * Initializes the XMLHttpRequest object.
   */
  #initRequest() {
    const xhr = (this.xhr = new XMLHttpRequest());
    xhr.open("POST", this.url, true);
    xhr.responseType = "json";
  }
  /**
   * Initializes XMLHttpRequest listeners.
   *
   * @param resolve Callback function to be called when the request is successful.
   * @param reject Callback function to be called when the request cannot be completed.
   * @param file File instance to be uploaded.
   */
  #initListeners(resolve, reject, file) {
    const xhr = this.xhr;
    const loader = this.loader;
    const t = this.t;
    const genericError = t("Cannot upload file:") + ` ${file.name}.`;
    xhr.addEventListener("error", () => reject(genericError));
    xhr.addEventListener("abort", () => reject());
    xhr.addEventListener("load", () => {
      const response = xhr.response;
      if (!response || !response.uploaded) {
        return reject(response && response.error && response.error.message ? response.error.message : genericError);
      }
      resolve({
        default: response.url,
      });
    });
    // Upload progress when it's supported.
    /* istanbul ignore else -- @preserve */
    if (xhr.upload) {
      xhr.upload.addEventListener("progress", (evt) => {
        if (evt.lengthComputable) {
          loader.uploadTotal = evt.total;
          loader.uploaded = evt.loaded;
        }
      });
    }
  }
  /**
   * Prepares the data and sends the request.
   *
   * @param file File instance to be uploaded.
   */
  #sendRequest(file) {
    // Prepare form data.
    const data = new FormData();
    data.append("upload", file);
    // Send request.
    this.xhr.send(data);
  }
}
