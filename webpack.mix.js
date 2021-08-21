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
    'node_modules/jquery/dist/jquery.min.js',
    'node_modules/popper.js/dist/umd/popper.min.js',
    'node_modules/bootstrap-material-design/dist/js/bootstrap-material-design.min.js',
], 'public/static/js/build/noj.js');

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
], 'public/static/css/build/noj.css');

if (mix.inProduction()) {
    mix.version();
}

mix.copyDirectory('node_modules', 'public/static/library');
