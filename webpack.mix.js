const mix = require('laravel-mix');
const MonacoWebpackPlugin = require('monaco-editor-webpack-plugin');
const IgnoreEmitPlugin = require('ignore-emit-webpack-plugin');

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

// Pre-compile supporting libraries first
mix.ts('resources/ts/noj.ts', 'public/static/js/build/app.js');

// Export resources for Admin Portal
mix.styles([
    'resources/css/wemd-color-scheme.css',
    'node_modules/simplemde/dist/simplemde.min.css',
    'node_modules/codemirror/lib/codemirror.css',
    'node_modules/codemirror/addon/hint/show-hint.css',
], 'public/static/css/build/noj_admin.css');

mix.ts('resources/ts/app_admin.ts', 'public/static/js/build/app_admin.js');
mix.scripts([
    'public/static/js/build/app_admin.js',
    'node_modules/simplemde/dist/simplemde.min.js',
    'node_modules/marked/marked.min.js',
    'node_modules/codemirror/lib/codemirror.js',
    'node_modules/codemirror/addon/edit/matchbrackets.js',
    'node_modules/codemirror/addon/hint/show-hint.js',
    'node_modules/codemirror/mode/clike/clike.js',
], 'public/static/js/build/noj_admin.js');

// Compile JS libraries bundle
mix.scripts([
    'public/static/js/build/app.js',
    'node_modules/popper.js/dist/umd/popper.min.js',
    'node_modules/bootstrap-material-design/dist/js/bootstrap-material-design.min.js',
    'node_modules/pdfobject/pdfobject.min.js',
    'node_modules/jquery-datetimepicker/build/jquery.datetimepicker.full.min.js',
    'node_modules/noj-jquery-ui-sortable/dist/jquery-ui-sortable.min.js',
    'node_modules/parazoom/dist/parazoom.min.js',
    'node_modules/chart.js/dist/Chart.bundle.min.js',
    'node_modules/simplemde/dist/simplemde.min.js',
    'node_modules/marked/marked.min.js',
    'node_modules/dompurify/dist/purify.min.js',
    'node_modules/clipboard/dist/clipboard.min.js',
], 'public/static/js/build/noj.js');

// Debugger for dompurify
mix.copy('node_modules/dompurify/dist/purify.min.js.map', 'public/static/js/build/purify.min.js.map');

// Compile CSS libraries bundle
mix.styles([
    'node_modules/bootstrap-material-design/dist/css/bootstrap-material-design.min.css',
    'node_modules/animate.css/animate.min.css',
    'resources/css/wemd-color-scheme.css',
    'resources/css/main.css',
    'node_modules/jquery-datetimepicker/build/jquery.datetimepicker.min.css',
    'node_modules/simplemde/dist/simplemde.min.css',
], 'public/static/css/build/noj.css');

// Compile NOJ Editor
mix.ts('resources/ts/monaco-editor/monaco.ts', 'public/static/js/build/noj-editor.js');

if (mix.inProduction()) {
    mix.version();
}

mix.disableNotifications();

mix.options({
    postCss: [
        require('autoprefixer')
    ],
    fileLoaderDirs: {
        images: 'static/img',
        fonts: 'static/fonts'
    },
});

mix.webpackConfig({
    devtool: false,
    entry: {
        // Package each language's worker and give these filenames in `getWorkerUrl`
        'static/js/build/worker/editor': 'monaco-editor/esm/vs/editor/editor.worker.js',
        'static/js/build/worker/json': 'monaco-editor/esm/vs/language/json/json.worker',
        'static/js/build/worker/css': 'monaco-editor/esm/vs/language/css/css.worker',
        'static/js/build/worker/html': 'monaco-editor/esm/vs/language/html/html.worker',
        'static/js/build/worker/ts': 'monaco-editor/esm/vs/language/typescript/ts.worker',
    },
    module: {
        rules: [
            {
                test: /\.wasm$/,
                use: ['wasm-loader'],
            }
        ],
    },
    stats: {
        warnings: false,
    },
    // As suggested on:
    // https://github.com/NeekSandhu/monaco-editor-textmate/blame/45e137e5604504bcf744ef86215becbbb1482384/README.md#L58-L59
    //
    // Use the MonacoWebpackPlugin to disable all built-in tokenizers/languages.
    plugins: [
        new MonacoWebpackPlugin({
            filename: 'static/js/build/worker/[name].js',
            publicPath: '/',
            languages: [],
            features: ['accessibilityHelp', 'anchorSelect', 'bracketMatching', 'caretOperations', 'clipboard', 'codeAction', 'codelens', 'colorPicker', 'comment', 'contextmenu', 'coreCommands', 'cursorUndo', 'dnd', 'documentSymbols', 'find', 'folding', 'fontZoom', 'format', 'gotoError', 'gotoLine', 'gotoSymbol', 'hover', 'iPadShowKeyboard', 'inPlaceReplace', 'indentation', 'inlayHints', 'inlineCompletions', 'inspectTokens', 'linesOperations', 'linkedEditing', 'links', 'multicursor', 'parameterHints', 'quickCommand', 'quickHelp', 'quickOutline', 'referenceSearch', 'rename', 'smartSelect', 'snippets', 'suggest', 'toggleTabFocusMode', 'transpose', 'unusualLineTerminators', 'viewportSemanticTokens', 'wordHighlighter', 'wordOperations', 'wordPartOperations']
        }),
        new IgnoreEmitPlugin([/editor\.worker\.js/])
    ],
});
