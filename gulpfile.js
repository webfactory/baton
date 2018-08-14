var gulp = require('gulp');
var $ = require('gulp-load-plugins')(); /// loads all gulp plugins in $.*
var mergeStream = require('merge-stream');
var path = require('path');
var saveLicense = require('uglify-save-license');
var autoprefixer = require('autoprefixer');

var config = {

    "stylesheets": {
        "files": {
            "css/styles.css": [
                '../node_modules/bootstrap/dist/css/bootstrap.min.css'
            ],
        },
        "watch": ['{vendor,src,www}/**/*.{css,scss}', '!www/css/**']
    },

    "javascripts": {
        "files": {
            "js/scripts.js": [
                '../node_modules/jquery/dist/jquery.min.js',
                '../node_modules/bootstrap/dist/js/bootstrap.min.js',
                '../src/AppBundle/Resources/public/js/searchProjectsWithPackageVersionForm.js'
            ]
        },
        "watch": ['{vendor,src,www}/**/*.js', '!www/js/**']
    },

    "webdir": 'www',
    "libdir": 'vendor',
    "tempdir": 'var'
};

gulp.task('compile-stylesheets', function () {
    'use strict';

    var merger = mergeStream();

    var execOptions = {
        cwd: config.webdir,
        pipeStdout: true,
        libdir: config.libdir,
        sassCacheDir: config.tempdir + '/.sass-cache',
        sassOutputStyle: 'nested',
        maxBuffer: 500 * 1024    // 500 KB buffer size for SASS -> CSS-Rebase output
    };

    for (var key in config.stylesheets.files) {
        var destPath = config.webdir + "/" + key;

        $.util.log("Compile " + key + ": [" + (config.stylesheets.files[key].join(", ")) + "]");

        merger.add(
            gulp.src(config.stylesheets.files[key], { cwd: config.webdir, read: false })
                .pipe($.exec('sass --cache-location <%= options.sassCacheDir %> --scss --style <%= options.sassOutputStyle %> --load-path <%= options.libdir %> <%= file.path %>', execOptions))
                .on('error', function(err) { $.util.log(err.message); })
                .pipe($.cssUrlRebase({ root: path.dirname(key) }))
                .pipe($.sourcemaps.init())
                .pipe($.postcss([autoprefixer({ browsers: ['last 5 version'] })]))
                .pipe($.concat(key))
                .pipe($.cleanCss({
                    compatibility: 'ie7',
                    rebase: false   // URL is better handled by cssUrlRebase
                }))
                .pipe($.sourcemaps.write())
                .pipe(gulp.dest(config.webdir))
        );
    }

    merger.pipe($.livereload({ auto: false }));
    return merger;
});

gulp.task('compile-javascripts', function() {
    'use strict';

    var merger = mergeStream();

    for (var key in config.javascripts.files) {
        $.util.log("Compile " + key + ": [" + (config.javascripts.files[key].join(", ")) + "]");

        merger.add(
            gulp.src(config.javascripts.files[key], { cwd: config.webdir })
                .pipe($.sourcemaps.init())
                .pipe($.uglify({
                    output: { comments: saveLicense }
                }))
                .pipe($.concat(key))
                .pipe($.sourcemaps.write())
                .pipe(gulp.dest(config.webdir))
        );
    }

    merger.pipe($.livereload({ auto: false }));
    return merger;

});

gulp.task('watch-with-livereload', function () {
    'use strict';

    $.livereload.listen();
    gulp.watch(config.stylesheets.watch, $.batch({ timeout: 20 }, function (done) {
        gulp.start('compile-stylesheets');
        done();
    }));

    gulp.watch(config.javascripts.watch, $.batch({ timeout: 20 }, function (done) {
        gulp.start('compile-javascripts');
        done();
    }));
});

gulp.task('default', ['watch-with-livereload']);
gulp.task('compile', ['compile-stylesheets', 'compile-javascripts']);
