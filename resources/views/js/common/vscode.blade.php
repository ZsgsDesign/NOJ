@include('js.common.vscodeThemes')
<script type="text/javascript" src="/static/library/monaco-editor/min/vs/loader.js"></script>
<script>
    window.addEventListener("load",function() {
        require.config({
            paths: {
                vs: '{{url('/static/library/monaco-editor/min/vs')}}',
                tokenizer: '{{url('/static/library/monaco-ace-tokenizer/dist')}}'
            },
            'vs/nls' : {
                availableLanguages: {
                    '*': '{{vscodeLocale()}}'
                }
            }
        });

        // Before loading vs/editor/editor.main, define a global MonacoEnvironment that overwrites
        // the default worker url location (used when creating WebWorkers). The problem here is that
        // HTML5 does not allow cross-domain web workers, so we need to proxy the instantiation of
        // a web worker through a same-domain script

        window.MonacoEnvironment = {
            getWorkerUrl: function(workerId, label) {
                return `data:text/javascript;charset=utf-8,${encodeURIComponent(`
                self.MonacoEnvironment = {
                    baseUrl: '{{url('/static/library/monaco-editor/min/')}}'
                };
                importScripts('{{url('/static/library/monaco-editor/min/vs/base/worker/workerMain.js')}}');`
                )}`;
            }
        };

        require(["vs/editor/editor.main"], async function () {
            require([
                'tokenizer/monaco-tokenizer',
                'tokenizer/definitions/haskell',
            ],function(
                MonacoAceTokenizer,
                HaskellDefinition
            ){
                monaco.languages.register({
                    id: 'haskell',
                    aliases: [
                        "Haskell",
                        "haskell"
                    ],
                    extensions: [
                        ".hs"
                    ]
                });
                MonacoAceTokenizer.registerRulesForLanguage('haskell', new HaskellDefinition.default);
                monaco.languages.setLanguageConfiguration('haskell', {
                    comments: {
                        lineComment: '--',
                        blockComment: ['{-', '-}']
                    },
                    brackets: [
                    ['{', '}'],
                    ['[', ']'],
                    ['(', ')']
                    ],
                    autoClosingPairs: [
                        { open: '{', close: '}' },
                        { open: '[', close: ']' },
                        { open: '(', close: ')' },
                        { open: '\'', close: '\'', notIn: ['string'] },
                        { open: '`', close: '`', notIn: ['string', 'comment'] }
                    ],
                    surroundingPairs: [
                        ['{', '}'],
                        ['[', ']'],
                        ['(', ')'],
                        ['\'', '\''],
                        ['"', '"'],
                        ['`', '`']
                    ],
                    indentationRules: {
                        decreaseIndentPattern: new RegExp("[\\]})][ \\t]*$/m"),
                        increaseIndentPattern: new RegExp("((\\b(if\\b.*|then|else|do|of|let|in|where))|=|->|>>=|>=>|=<<|(^(data)( |\t)+(\\w|')+( |\\t)*))( |\\t)*$/")
                    }
                });
            });

            var themes = Object.keys(loadedThemesData);
            themes.forEach(theme => {
                monaco.editor.defineTheme(theme, loadedThemesData[theme]);
            });

            {{ $slot }}
        });
}, false);
</script>
