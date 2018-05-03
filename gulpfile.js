/*global require:false */

var gulp = require('gulp');
var $ = require('gulp-load-plugins')(); /// lädt alle gulp-*-Module in $.*
var mergeStream = require('merge-stream');
var path = require('path');
var saveLicense = require('uglify-save-license');
var autoprefixer = require('autoprefixer');

var config = {

    "stylesheets": {
        "files": {
            // Alle Pfade relativ zu www/, *nicht* mit ../.. aus www ausbrechen!
            // "css/target1.css": [ 'bundles/xx/scss/one.scss', 'bower_components/yy/yy.css' ],
            // "css/target2.css": [ 'bundles/xx/scss/two.scss', 'bower_components/zz/zz.css' ]
        },
        "watch": ['{vendor,src,www}/**/*.{css,scss}', '!www/css/**']
    },

    "javascripts": {
        "files": {
            "js/scripts.js": [
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
        maxBuffer: 500 * 1024    // 500 KB Puffergröße für Ausgabe SASS -> CSS-Rebase
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
                    rebase: false   // URL rebasing wird besser von cssUrlRebase gehandhabt
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
