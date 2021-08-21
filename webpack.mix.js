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
    'node_modules/pdfobject/pdfobject.min.js',
    'node_modules/jquery-datetimepicker/build/jquery.datetimepicker.full.min.js',
    'node_modules/noj-jquery-ui-sortable/dist/jquery-ui-sortable.min.js',
    'node_modules/parazoom/dist/parazoom.min.js',
    'node_modules/chart.js/dist/Chart.bundle.min.js',
    'node_modules/highlightjs/highlight.pack.min.js',
    'node_modules/simplemde/dist/simplemde.min.js',
    'node_modules/marked/marked.min.js',
    'node_modules/dompurify/dist/purify.min.js',
], 'public/static/js/build/noj.js');

mix.styles([
    'node_modules/bootstrap-material-design/dist/css/bootstrap-material-design.min.css',
    'node_modules/animate.css/animate.min.css',
    'resources/css/wemd-color-scheme.css',
    'resources/css/main.css',
    'node_modules/jquery-datetimepicker/build/jquery.datetimepicker.min.css',
    'node_modules/simplemde/dist/simplemde.min.css',
], 'public/static/css/build/noj.css');

mix.styles([
    'resources/css/wemd-color-scheme.css',
], 'public/static/css/build/color.css');

mix.ts('resources/ts/monaco-editor/monaco.ts', 'public/static/js/build/noj-monaco.js');

if (mix.inProduction()) {
    mix.version();
}
