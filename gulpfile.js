const gulp = require('gulp');
const $ = require('./node_modules/webfactory-gulp-preset/plugins')(); // loads all gulp-* modules in $.* for easy reference

const config = require('./gulp-config');

// Explicitly declare the Sass compiler – node-sass is the current default compiler in gulp-sass,
// but we want to be future-compatible in case this changes;
// fyi: the new canonical Sass Implementation is dart-sass (https://github.com/sass/dart-sass)
$.sass.compiler = require('node-sass');

const { scripts } = require('./node_modules/webfactory-gulp-preset/tasks/scripts');
const { styles } = require('./node_modules/webfactory-gulp-preset/tasks/styles');
const { browsersync } = require('./node_modules/webfactory-gulp-preset/tasks/browsersync');

function js(cb) {
    scripts(gulp, $, config);
    cb();
}

function css(cb) {
    styles(gulp, $, config);
    cb();
}

function serve(cb) {
    browsersync(gulp, $, config, css, js);
    cb();
}

exports.js = js;
exports.css = css;
exports.serve = serve;
exports.compile = gulp.parallel(css, js);
exports.default = gulp.series(gulp.parallel(css, js), serve);
