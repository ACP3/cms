/*
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

module.exports = function (gulp, plugins) {
    "use strict";

    return function () {
        var nodeBasePath = './node_modules',
            systemBasePath = './ACP3/Modules/ACP3/System/Resources/Assets',
            paths = [
                {
                    'src': [
                        nodeBasePath + '/jquery/dist/jquery.min.js',
                        nodeBasePath + '/bootbox/bootbox.js',
                        nodeBasePath + '/moment/min/moment.min.js',
                        nodeBasePath + '/datatables.net/js/jquery.dataTables.js',
                        nodeBasePath + '/datatables.net-bs/js/dataTables.bootstrap.js',
                        nodeBasePath + '/bootstrap/dist/js/bootstrap.min.js',
                        nodeBasePath + '/fancybox/source/jquery.fancybox.pack.js',
                        nodeBasePath + '/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
                        nodeBasePath + '/html5shiv/dist/html5shiv.min.js'
                    ],
                    'dest': systemBasePath + '/js'
                },
                {
                    'src': nodeBasePath + '/bootstrap/dist/fonts/*',
                    'dest': systemBasePath + '/fonts'
                },
                {
                    'src': [
                        nodeBasePath + '/fancybox/source/*.gif',
                        nodeBasePath + '/fancybox/source/*.png'
                    ],
                    'dest': systemBasePath + '/images/fancybox'
                },
                {
                    'src': [
                        nodeBasePath + '/bootstrap/dist/css/bootstrap.min.css',
                        nodeBasePath + '/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
                        nodeBasePath + '/datatables.net-bs/css/dataTables.bootstrap.css'

                    ],
                    'dest': systemBasePath + '/css'
                }
            ];

        for (var i = 0; i < paths.length; i++) {
            gulp.src(paths[i].src)
                .pipe(gulp.dest(paths[i].dest));
        }

        return gulp.src(nodeBasePath + '/fancybox/source/jquery.fancybox.css')
            .pipe(plugins.modifyCssUrls({
                prepend: '../images/fancybox/'
            }))
            .pipe(gulp.dest(systemBasePath + '/css'));
    }
};
