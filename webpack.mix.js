const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// mix.js('resources/js/app.js', 'public/js')
//    .sass('resources/sass/app.scss', 'public/css');

mix.scripts([
    'public/js/admin.js',
    'public/js/dashboard.js'
], 'public/static/js/noj.js');

mix.styles([
    'public/static/fonts/roboto/roboto.css',
    'public/static/fonts/roboto/roboto.css',
    'public/static/fonts/montserrat/montserrat.css',
    'public/static/fonts/roboto-slab/roboto-slab.css',
    'public/static/fonts/mdi-wxss/MDI.css',
    'public/static/fonts/devicon/devicon.min.css',
    'public/static/fonts/langicon/langicon.css',
    'public/static/fonts/socialicon/socialicon.css',
    'node_modules/bootstrap-material-design/dist/css/bootstrap-material-design.min.css',
    'node_modules/animate.css/animate.min.css',
    'public/static/css/wemd-color-scheme.css',
    'public/static/css/main.css',
], 'public/static/css/noj.css').options({
    processCssUrls: false
});

mix.copyDirectory('node_modules', 'public/static/library');
