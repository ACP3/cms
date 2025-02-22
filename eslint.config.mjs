import globals from "globals";
import path from "node:path";
import { fileURLToPath } from "node:url";
import js from "@eslint/js";
import { FlatCompat } from "@eslint/eslintrc";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const compat = new FlatCompat({
  baseDirectory: __dirname,
  recommendedConfig: js.configs.recommended,
  allConfig: js.configs.all,
});

export default [
  {
    ignores: [
      "**/*min.js",
      "ACP3/Modules/ACP3/Cookieconsent/Resources/Assets/js/klaro-no-css.js",
      "ACP3/Modules/ACP3/Filemanager/Resources/Assets/rich-filemanager/*",
      "ACP3/Modules/ACP3/Wysiwygckeditor/Resources/Assets/js/ckeditor/*",
      "ACP3/Modules/ACP3/Wysiwygtinymce/Resources/Assets/js/tinymce/*",
      "ACP3/Modules/ACP3/System/Resources/Assets/js/dataTables.bootstrap5.js",
      "**/build",
      "**/cache",
      "**/node_modules",
      "**/vendor",
      "**/uploads",
    ],
  },
  ...compat.extends("eslint:recommended", "prettier"),
  {
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.jquery,
        ...globals.node,
        grecaptcha: false,
        Cookies: false,
      },

      ecmaVersion: 2022,
      sourceType: "module",
    },

    rules: {
      "no-dupe-class-members": "off",

      "no-unused-vars": [
        "error",
        {
          vars: "local",
        },
      ],

      "no-var": "error",

      "no-console": [
        "error",
        {
          allow: ["warn", "error"],
        },
      ],
    },
  },
];
