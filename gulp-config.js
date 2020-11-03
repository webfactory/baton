const argv = require('minimist')(process.argv.slice(2));

// roll your own function if you need to use more or different plugins
const { postCssPlugins } = require('./node_modules/webfactory-gulp-preset/config/postcss-plugins-default');

module.exports = {
    scripts: {
        files: [
            {
                name: 'scripts.js',
                files: [
                    '../node_modules/jquery/dist/jquery.min.js',
                    '../node_modules/bootstrap/dist/js/bootstrap.min.js',
                    '../src/AppBundle/Resources/public/js/searchProjectsWithPackageVersionForm.js',
                ],
                destDir: 'js'
            }
        ],
        watch: ['{vendor,src,www}/**/*.js', '!www/js/**'],
    },
    styles: {
        files: [
            {
                name: 'styles.css',
                files: [
                    '../node_modules/bootstrap/dist/css/bootstrap.min.css',
                ],
                destDir: 'css'
            }
        ],
        watch: ['{vendor,src,www}/**/*.{css,scss}', '!www/css/**'],
        postCssPlugins: postCssPlugins
    },
    stylelint: {
        files: [
            'PATH_TO_PROJECT_ASSETS_DIR/scss/**/*.scss'
        ],
        destDir: 'PATH_TO_PROJECT_ASSETS_DIR/scss'
    },

    "development": (argv.env || process.env.APP_ENV || 'development') === 'development',
    "webdir": "www",
    "libdir": "vendor",
    "tempdir": "tmp",
    "npmdir": "node_modules"
}