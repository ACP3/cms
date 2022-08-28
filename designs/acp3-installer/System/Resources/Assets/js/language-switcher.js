/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

import LanguageSwitcher from "./lib/language-switcher";

(() => {
  const languageSwitcher = new LanguageSwitcher("languages");
  languageSwitcher.init();
})();
