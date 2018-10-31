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
                        nodeBasePath + '/bootbox/bootbox.min.js',
                        nodeBasePath + '/moment/min/moment-with-locales.min.js',
                        nodeBasePath + '/datatables.net/js/jquery.dataTables.min.js',
                        nodeBasePath + '/datatables.net-bs4/js/dataTables.bootstrap4.js',
                        nodeBasePath + '/bootstrap/dist/js/bootstrap.min.js',
                        nodeBasePath + '/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.min.js',
                        nodeBasePath + '/tempusdominus-core/build/js/tempusdominus-core.js',
                        nodeBasePath + '/html5shiv/dist/html5shiv.min.js',
                        nodeBasePath + '/popper.js/dist/umd/popper.min.js',
                        nodeBasePath + '/js-cookie/src/js.cookie.js'
                    ],
                    'dest': systemBasePath + '/js'
                },
                {
                    'src': [
                        nodeBasePath + '/@fortawesome/fontawesome-free/webfonts/**/*'
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
                        nodeBasePath + '/tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.css',
                        nodeBasePath + '/@fortawesome/fontawesome-free/css/all.min.css',
                        nodeBasePath + '/datatables.net-bs4/css/dataTables.bootstrap4.css'

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
