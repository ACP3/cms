/*
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

module.exports = function(gulp, $) {
    "use strict";

    gulp.task('copy', function () {
        var bowerBasePath = './bower_components',
            systemBasePath = './ACP3/Modules/ACP3/System/Resources/Assets',
            paths = [
                {
                    'src': [
                        bowerBasePath + '/jquery/dist/jquery.min.js',
                        bowerBasePath + '/bootbox.js/bootbox.js',
                        bowerBasePath + '/moment/min/moment.min.js',
                        bowerBasePath + '/datatables.net/js/jquery.dataTables.min.js',
                        bowerBasePath + '/datatables.net-bs/js/dataTables.bootstrap.js',
                        bowerBasePath + '/bootstrap/dist/js/bootstrap.min.js',
                        bowerBasePath + '/fancybox/source/jquery.fancybox.pack.js',
                        bowerBasePath + '/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
                        bowerBasePath + '/html5shiv/dist/html5shiv.min.js'
                    ],
                    'dest': systemBasePath + '/js/libs'
                },
                {
                    'src': bowerBasePath + '/bootstrap/dist/fonts/*',
                    'dest': systemBasePath + '/fonts'
                },
                {
                    'src': [
                        bowerBasePath + '/fancybox/source/*.gif',
                        bowerBasePath + '/fancybox/source/*.png'
                    ],
                    'dest': systemBasePath + '/images/fancybox'
                },
                {
                    'src': [
                        bowerBasePath + '/bootstrap/dist/css/bootstrap.min.css',
                        bowerBasePath + '/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
                        bowerBasePath + '/datatables.net-bs/css/dataTables.bootstrap.css'

                    ],
                    'dest': systemBasePath + '/css'
                }
            ];

        for (var i = 0; i < paths.length; i++) {
            gulp.src(paths[i].src)
                .pipe(gulp.dest(paths[i].dest));
        }

        return gulp.src(bowerBasePath + '/fancybox/source/jquery.fancybox.css')
            .pipe($.modifyCssUrls({
                prepend: '../images/fancybox/'
            }))
            .pipe(gulp.dest(systemBasePath + '/css'));
    });
};
