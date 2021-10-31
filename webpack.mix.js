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

// Pre Compile NOJ JS Supporting Libraries - app.temp.js
mix.ts('resources/ts/app.ts', 'public/static/js/build/app.temp.js');

// Pre Compile NOJ CSS Supporting Libraries - app.temp.css
mix.sass('resources/sass/app.scss', 'public/static/css/build/app.temp.css');

// Pre Compile NOJ Admin Portal JS Supporting Libraries - app.admin.temp.js
mix.ts('resources/ts/admin.ts', 'public/static/js/build/app.admin.temp.js');

// Pre Compile NOJ Admin Portal CSS Supporting Libraries - app.admin.temp.css
mix.sass('resources/sass/admin.scss', 'public/static/css/build/app.admin.temp.css');

// Compile NOJ Admin Portal CSS Libraries Bundle - app.admin.css
mix.styles([
    'public/static/css/build/app.admin.temp.css',
    'node_modules/simplemde/dist/simplemde.min.css',
    'node_modules/codemirror/lib/codemirror.css',
    'node_modules/codemirror/addon/hint/show-hint.css',
    'node_modules/highlight.js/styles/vs.css',
    'node_modules/github-markdown-css/github-markdown-light.css',
], 'public/static/css/build/app.admin.css');

// Compile NOJ Admin Portal JS Libraries Bundle - app.admin.js
mix.scripts([
    'public/static/js/build/app.admin.temp.js',
    'node_modules/simplemde/dist/simplemde.min.js',
    'node_modules/marked/marked.min.js',
    'node_modules/codemirror/lib/codemirror.js',
    'node_modules/codemirror/addon/edit/matchbrackets.js',
    'node_modules/codemirror/addon/hint/show-hint.js',
    'node_modules/codemirror/mode/clike/clike.js',
], 'public/static/js/build/app.admin.js');

// Compile NOJ JS Libraries Bundle - app.js
mix.scripts([
    'public/static/js/build/app.temp.js',
    'node_modules/bootstrap-material-design/dist/js/bootstrap-material-design.min.js',
    'node_modules/pdfobject/pdfobject.min.js',
    'node_modules/jquery-datetimepicker/build/jquery.datetimepicker.full.min.js',
    'node_modules/parazoom/dist/parazoom.min.js',
    'node_modules/chart.js/dist/Chart.bundle.min.js',
    'node_modules/simplemde/dist/simplemde.min.js',
    'node_modules/marked/marked.min.js',
    'node_modules/clipboard/dist/clipboard.min.js',
], 'public/static/js/build/app.js');

// Compile NOJ PDF Compiler Libraries Bundle - paged.polyfill.js
mix.scripts([
    'node_modules/pagedjs/dist/paged.polyfill.js',
], 'public/static/js/build/paged.polyfill.js');

// Compile NOJ CSS Libraries Bundle - app.css
mix.styles([
    'public/static/css/build/app.temp.css',
    'node_modules/bootstrap-material-design/dist/css/bootstrap-material-design.min.css',
    'node_modules/animate.css/animate.min.css',
    'resources/css/main.css',
    'node_modules/jquery-datetimepicker/build/jquery.datetimepicker.min.css',
    'node_modules/simplemde/dist/simplemde.min.css',
    'node_modules/github-markdown-css/github-markdown-light.css',
], 'public/static/css/build/app.css');

// Compile NOJ Editor - app.editor.js
mix.ts('resources/ts/monaco-editor/monaco.ts', 'public/static/js/build/app.editor.js');

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
        // 'static/js/build/worker/editor': 'monaco-editor/esm/vs/editor/editor.worker.js',
        // 'static/js/build/worker/json': 'monaco-editor/esm/vs/language/json/json.worker',
        // 'static/js/build/worker/css': 'monaco-editor/esm/vs/language/css/css.worker',
        // 'static/js/build/worker/html': 'monaco-editor/esm/vs/language/html/html.worker',
        // 'static/js/build/worker/ts': 'monaco-editor/esm/vs/language/typescript/ts.worker',
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
        })
    ],
});
