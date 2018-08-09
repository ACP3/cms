/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

module.exports = function (gulp) {
    'use strict';

    return function (done) {
        const nodeBasePath = './node_modules',
            systemBasePath = './ACP3/Modules/ACP3/System/Resources/Assets',
            ckeditorBasePath = './ACP3/Modules/ACP3/Wysiwygckeditor/Resources/Assets',
            shareBasePath = './ACP3/Modules/ACP3/Share/Resources/Assets',
            paths = [
                {
                    'src': [
                        nodeBasePath + '/@fancyapps/fancybox/dist/jquery.fancybox.min.js',
                        nodeBasePath + '/jquery/dist/jquery.min.js',
                        nodeBasePath + '/bootbox/bootbox.js',
                        nodeBasePath + '/moment/min/moment.min.js',
                        nodeBasePath + '/datatables.net/js/jquery.dataTables.js',
                        nodeBasePath + '/datatables.net-bs/js/dataTables.bootstrap.js',
                        nodeBasePath + '/bootstrap/dist/js/bootstrap.min.js',
                        nodeBasePath + '/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
                        nodeBasePath + '/html5shiv/dist/html5shiv.min.js',
                        nodeBasePath + '/js-cookie/src/js.cookie.js'
                    ],
                    'dest': systemBasePath + '/js'
                },
                {
                    'src': [
                        nodeBasePath + '/bootstrap/dist/fonts/*'
                    ],
                    'dest': systemBasePath + '/fonts'
                },
                {
                    'src': [
                        nodeBasePath + '/@fortawesome/fontawesome-free-webfonts/webfonts/**/*'
                    ],
                    'dest': systemBasePath + '/webfonts'
                },
                {
                    'src': nodeBasePath + '/ckeditor-codemirror-plugin/codemirror/**',
                    'dest': ckeditorBasePath + '/js/ckeditor/plugins/codemirror'
                },
                {
                    'src': nodeBasePath + '/shariff/dist/shariff.min.css',
                    'dest': shareBasePath + '/css'
                },
                {
                    'src': nodeBasePath + '/shariff/dist/shariff.min.js',
                    'dest': shareBasePath + '/js'
                },
                {
                    'src': [
                        nodeBasePath + '/@fancyapps/fancybox/dist/jquery.fancybox.css',
                        nodeBasePath + '/bootstrap/dist/css/bootstrap.min.css',
                        nodeBasePath + '/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
                        nodeBasePath + '/@fortawesome/fontawesome-free-webfonts/css/fa-brands.css',
                        nodeBasePath + '/@fortawesome/fontawesome-free-webfonts/css/fa-regular.css',
                        nodeBasePath + '/@fortawesome/fontawesome-free-webfonts/css/fa-solid.css',
                        nodeBasePath + '/@fortawesome/fontawesome-free-webfonts/css/fontawesome.css',
                        nodeBasePath + '/datatables.net-bs/css/dataTables.bootstrap.css'

                    ],
                    'dest': systemBasePath + '/css'
                }
            ];

        for (const path of paths) {
            gulp.src(path.src)
                .pipe(gulp.dest(path.dest));
        }

        return done();
    };
};
