/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

module.exports = (gulp) => {
  "use strict";

  return (done) => {
    const nodeBasePath = "./node_modules",
      systemBasePath = "./ACP3/Modules/ACP3/System/Resources/Assets",
      fileManagerBasePath = "./ACP3/Modules/ACP3/Filemanager/Resources/Assets",
      consentManagerBasePath = "./ACP3/Modules/ACP3/Cookieconsent/Resources/Assets",
      shareBasePath = "./ACP3/Modules/ACP3/Share/Resources/Assets",
      wysiwygCKEditorBasePath = "./ACP3/Modules/ACP3/Wysiwygckeditor/Resources/Assets",
      wysiwygTinyMCEBasePath = "./ACP3/Modules/ACP3/Wysiwygtinymce/Resources/Assets",
      paths = [
        {
          src: [
            nodeBasePath + "/@fancyapps/fancybox/dist/jquery.fancybox.min.js",
            nodeBasePath + "/jquery/dist/jquery.min.js",
            nodeBasePath + "/bootstrap/dist/js/bootstrap.bundle.min.js",
            nodeBasePath + "/datatables.net/js/jquery.dataTables.min.js",
            nodeBasePath + "/datatables.net-bs5/js/dataTables.bootstrap5.js",
            nodeBasePath + "/js-cookie/dist/js.cookie.min.js",
            nodeBasePath + "/photoswipe/dist/umd/photoswipe.umd.min.js",
            nodeBasePath + "/photoswipe/dist/umd/photoswipe-lightbox.umd.min.js",
          ],
          dest: systemBasePath + "/js",
        },
        {
          src: [nodeBasePath + "/bootstrap/dist/fonts/*"],
          dest: systemBasePath + "/fonts",
        },
        {
          src: [nodeBasePath + "/@fortawesome/fontawesome-free/webfonts/**/*"],
          dest: systemBasePath + "/webfonts",
        },
        {
          src: [nodeBasePath + "/@fortawesome/fontawesome-free/svgs/**/*"],
          dest: systemBasePath + "/svgs",
        },
        {
          src: nodeBasePath + "/shariff/dist/shariff.min.css",
          dest: shareBasePath + "/css",
        },
        {
          src: nodeBasePath + "/shariff/dist/shariff.min.js",
          dest: shareBasePath + "/js",
        },
        {
          src: [
            nodeBasePath + "/@fancyapps/fancybox/dist/jquery.fancybox.css",
            nodeBasePath + "/@fortawesome/fontawesome-free/css/all.css",
            nodeBasePath + "/datatables.net-bs5/css/dataTables.bootstrap5.css",
            nodeBasePath + "/photoswipe/dist/photoswipe.css",
          ],
          dest: systemBasePath + "/css",
        },
        {
          src: nodeBasePath + "/rich-filemanager/index.html",
          dest: fileManagerBasePath + "/rich-filemanager",
        },
        {
          src: nodeBasePath + "/rich-filemanager/images/**",
          dest: fileManagerBasePath + "/rich-filemanager/images",
        },
        {
          src: nodeBasePath + "/rich-filemanager/languages/**",
          dest: fileManagerBasePath + "/rich-filemanager/languages",
        },
        {
          src: nodeBasePath + "/rich-filemanager/libs/**",
          dest: fileManagerBasePath + "/rich-filemanager/libs",
        },
        {
          src: nodeBasePath + "/rich-filemanager/src/**",
          dest: fileManagerBasePath + "/rich-filemanager/src",
        },
        {
          src: nodeBasePath + "/rich-filemanager/themes/**",
          dest: fileManagerBasePath + "/rich-filemanager/themes",
        },
        {
          src: nodeBasePath + "/klaro/dist/klaro.css",
          dest: consentManagerBasePath + "/css",
        },
        {
          src: nodeBasePath + "/klaro/dist/klaro-no-css.js",
          dest: consentManagerBasePath + "/js",
        },
        {
          src: nodeBasePath + "/ckeditor4/{ckeditor.js,config.js,styles.js}",
          dest: wysiwygCKEditorBasePath + "/js/ckeditor",
        },
        {
          src: nodeBasePath + "/ckeditor4/lang/**/*",
          dest: wysiwygCKEditorBasePath + "/js/ckeditor/lang",
        },
        {
          src: nodeBasePath + "/ckeditor4/plugins/**/*",
          dest: wysiwygCKEditorBasePath + "/js/ckeditor/plugins",
        },
        {
          src: nodeBasePath + "/ckeditor4/skins/moono-lisa/*",
          dest: wysiwygCKEditorBasePath + "/js/ckeditor/skins/moono-lisa",
        },
        {
          src: nodeBasePath + "/ckeditor-codemirror-plugin/codemirror/**",
          dest: wysiwygCKEditorBasePath + "/js/ckeditor/plugins/codemirror",
        },
        {
          src: [nodeBasePath + "/tinymce/tinymce.min.js"],
          dest: wysiwygTinyMCEBasePath + "/js/tinymce",
        },
        {
          src: [nodeBasePath + "/tinymce/plugins/**/*"],
          dest: wysiwygTinyMCEBasePath + "/js/tinymce/plugins",
        },
        {
          src: [nodeBasePath + "/tinymce/skins/**/*"],
          dest: wysiwygTinyMCEBasePath + "/js/tinymce/skins",
        },
        {
          src: [nodeBasePath + "/tinymce/themes/**/*"],
          dest: wysiwygTinyMCEBasePath + "/js/tinymce/themes",
        },
      ];

    for (const path of paths) {
      gulp.src(path.src).pipe(gulp.dest(path.dest));
    }

    return done();
  };
};
