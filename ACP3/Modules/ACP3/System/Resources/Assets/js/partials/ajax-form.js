/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

import { AjaxForm } from "../lib/ajax-form";
import { LoadingIndicator } from "../lib/loading-indicator";
import { FormValidator } from "../lib/form-validator";

(() => {
  new AjaxForm(new LoadingIndicator(), new FormValidator());
})();
