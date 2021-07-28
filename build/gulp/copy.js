/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

module.exports = (gulp) => {
  "use strict";

  return (done) => {
    const nodeBasePath = "./node_modules",
      systemBasePath = "./ACP3/Modules/ACP3/System/Resources/Assets",
      ckeditorBasePath = "./ACP3/Modules/ACP3/Wysiwygckeditor/Resources/Assets",
      fileManagerBasePath = "./ACP3/Modules/ACP3/Filemanager/Resources/Assets",
      consentManagerBasePath = "./ACP3/Modules/ACP3/Cookieconsent/Resources/Assets",
      shareBasePath = "./ACP3/Modules/ACP3/Share/Resources/Assets",
      paths = [
        {
          src: [
            nodeBasePath + "/@fancyapps/fancybox/dist/jquery.fancybox.min.js",
            nodeBasePath + "/jquery/dist/jquery.min.js",
            nodeBasePath + "/bootbox/dist/bootbox.all.min.js",
            nodeBasePath + "/datatables.net/js/jquery.dataTables.js",
            nodeBasePath + "/datatables.net-bs/js/dataTables.bootstrap.js",
            nodeBasePath + "/bootstrap-sass/assets/javascripts/bootstrap.min.js",
            nodeBasePath + "/flatpickr/dist/flatpickr.min.js",
            nodeBasePath + "/js-cookie/dist/js.cookie.min.js",
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
          src: [nodeBasePath + "/@fortawesome/fontawesome-free/sprites/**/*"],
          dest: systemBasePath + "/sprites",
        },
        {
          src: nodeBasePath + "/ckeditor-codemirror-plugin/codemirror/**",
          dest: ckeditorBasePath + "/js/ckeditor/plugins/codemirror",
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
            nodeBasePath + "/bootstrap/dist/css/bootstrap.min.css",
            nodeBasePath + "/flatpickr/dist/flatpickr.min.css",
            nodeBasePath + "/@fortawesome/fontawesome-free/css/all.css",
            nodeBasePath + "/datatables.net-bs/css/dataTables.bootstrap.css",
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
      ];

    for (const path of paths) {
      gulp.src(path.src).pipe(gulp.dest(path.dest));
    }

    return done();
  };
};
