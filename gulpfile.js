/*
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

var gulp = require('gulp'),
    rename = require('gulp-rename'),
    plumber = require('gulp-plumber'),
    less = require('gulp-less'),
    modifyCssUrls = require('gulp-modify-css-urls'),
    gutil = require('gulp-util'),
    change = require('gulp-change'),
    argv = require('yargs').argv;

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
        .pipe(modifyCssUrls({
            prepend: '../images/fancybox/'
        }))
        .pipe(gulp.dest(systemBasePath + '/css'));
});

gulp.task('bump-version', function () {
    if (argv.from === undefined || argv.to === undefined) {
        gutil.log(gutil.colors.red('Error: Please specify the arguments "from" and "to".'));
        return;
    }

    function replaceAll(str, find, replace) {
        return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
    }

    function escapeRegExp(str) {
        return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
    }

    return gulp.src(
        [
            './ACP3/Core/composer.json',
            './ACP3/Core/Application/BootstrapInterface.php',
            './ACP3/Modules/ACP3/*/composer.json',
            './installation/composer.json',
            './package.json'
        ],
        {
            base: './'
        }
    )
        .pipe(change(function (content) {
            return replaceAll(content, argv.from, argv.to);
        }))
        .pipe(gulp.dest('./'))
});

gulp.task('less', function () {
    return gulp.src(
        [
            './ACP3/Modules/*/*/Resources/Assets/less/style.less',
            './ACP3/Modules/*/*/Resources/Assets/less/append.less',
            './designs/*/*/Assets/less/style.less',
            './designs/*/*/Assets/less/append.less',
            './designs/*/Assets/less/*.less',
            './installation/design/Assets/less/*.less',
            './installation/Installer/Modules/*/Resources/Assets/less/style.less'
        ],
        {base: './'}
    )
        .pipe(plumber())
        .pipe(less())
        .pipe(rename(function (path) {
            path.dirname = path.dirname.substring(0, path.dirname.length - 4) + 'css'
        }))
        .pipe(gulp.dest('./'));
});

gulp.task('watch', function () {
    // Watch all the .less files, then run the less task
    return gulp.watch(
        [
            './ACP3/Modules/*/*/Resources/Assets/less/**/*.less',
            './designs/*/**/Assets/less/*.less'
        ],
        ['less']
    );
});

gulp.task('default', ['watch']);
