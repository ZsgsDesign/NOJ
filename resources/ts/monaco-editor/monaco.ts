import type { LanguageId } from './register';
import type { ScopeName, TextMateGrammar, ScopeNameInfo } from './providers';
import * as monaco from 'monaco-editor/esm/vs/editor/editor.api';
import { createOnigScanner, createOnigString, loadWASM } from 'vscode-oniguruma';
import { SimpleLanguageInfoProvider } from './providers';
import { registerLanguages } from './register';
import { rehydrateRegexps } from './configuration';
import VsCodeDarkTheme from './themes/vs-dark-plus-theme';
import { IRawTheme } from 'vscode-textmate';

self.MonacoEnvironment = {
    getWorkerUrl: function (moduleId, label) {
        if (label === 'json') {
            return '/static/js/build/worker/json.js';
        }
        if (label === 'css' || label === 'scss' || label === 'less') {
            return '/static/js/build/worker/css.js';
        }
        if (label === 'html' || label === 'handlebars' || label === 'razor') {
            return '/static/js/build/worker/html.js';
        }
        if (label === 'typescript' || label === 'javascript') {
            return '/static/js/build/worker/ts.js';
        }
        return '/static/js/build/worker/editor.js';
    }
}

interface DemoScopeNameInfo extends ScopeNameInfo {
    path: string;
}

// main('python', 'vs', 'vscode_container');

async function main(language: LanguageId, themeKey: string, elementID: string) {
    // In this demo, the following values are hardcoded to support Python using
    // the VS Code Dark+ theme. Currently, end users are responsible for
    // extracting the data from the relevant VS Code extensions themselves to
    // leverage other TextMate grammars or themes. Scripts may be provided to
    // facilitate this in the future.
    //
    // Note that adding a new TextMate grammar entails the following:
    // - adding an entry in the languages array
    // - adding an entry in the grammars map
    // - making the TextMate file available in the grammars/ folder
    // - making the monaco.languages.LanguageConfiguration available in the
    //   configurations/ folder.
    //
    // You likely also want to add an entry in getSampleCodeForLanguage() and
    // change the call to main() above to pass your LanguageId.
    const languages: monaco.languages.ILanguageExtensionPoint[] = [
        {
            id: 'python',
            extensions: [
                '.py',
                '.rpy',
                '.pyw',
                '.cpy',
                '.gyp',
                '.gypi',
                '.pyi',
                '.ipy',
                '.bzl',
                '.cconf',
                '.cinc',
                '.mcconf',
                '.sky',
                '.td',
                '.tw',
            ],
            aliases: ['Python', 'py'],
            filenames: ['Snakefile', 'BUILD', 'BUCK', 'TARGETS'],
            firstLine: '^#!\\s*/?.*\\bpython[0-9.-]*\\b',
        },
    ];
    const grammars: { [scopeName: string]: DemoScopeNameInfo } = {
        'source.python': {
            language: 'python',
            path: 'python.tmLanguage.json',
        },
    };

    const fetchGrammar = async (scopeName: ScopeName): Promise<TextMateGrammar> => {
        const { path } = grammars[scopeName];
        const uri = `/static/language-services/grammars/${path}`;
        const response = await fetch(uri);
        const grammar = await response.text();
        const type = path.endsWith('.json') ? 'json' : 'plist';
        return { type, grammar };
    };

    const fetchConfiguration = async (
        language: LanguageId,
    ): Promise<monaco.languages.LanguageConfiguration> => {
        const uri = `/static/language-services/configurations/${language}.json`;
        const response = await fetch(uri);
        const rawConfiguration = await response.text();
        return rehydrateRegexps(rawConfiguration);
    };

    const data: ArrayBuffer | Response = await loadVSCodeOnigurumWASM();
    loadWASM(data);
    const onigLib = Promise.resolve({
        createOnigScanner,
        createOnigString,
    });

    const provider = new SimpleLanguageInfoProvider({
        grammars,
        fetchGrammar,
        configurations: languages.map((language) => language.id),
        fetchConfiguration,
        theme: getTheme(themeKey),
        onigLib,
        monaco,
    });
    registerLanguages(
        languages,
        (language: LanguageId) => provider.fetchLanguageInfo(language),
        monaco,
    );

    const value = getSampleCodeForLanguage(language);
    const id = elementID;
    const element = document.getElementById(id);
    if (element == null) {
        throw Error(`could not find element #${id}`);
    }

    let editor = monaco.editor.create(element, {
        value,
        language,
        theme: themeKey,
        fontSize: 16,
        formatOnPaste: true,
        formatOnType: true,
        automaticLayout: true,
        minimap: {
            enabled: true,
        },
    });

    provider.injectCSS();

    return editor;
}

// Taken from https://github.com/microsoft/vscode/blob/829230a5a83768a3494ebbc61144e7cde9105c73/src/vs/workbench/services/textMate/browser/textMateService.ts#L33-L40
async function loadVSCodeOnigurumWASM(): Promise<Response | ArrayBuffer> {
    const response = await fetch('/static/library/vscode-oniguruma/release/onig.wasm');
    const contentType = response.headers.get('content-type');
    if (contentType === 'application/wasm') {
        return response;
    }

    // Using the response directly only works if the server sets the MIME type 'application/wasm'.
    // Otherwise, a TypeError is thrown when using the streaming compiler.
    // We therefore use the non-streaming compiler :(.
    return await response.arrayBuffer();
}

function getSampleCodeForLanguage(language: LanguageId): string {
    if (language === 'python') {
        return `\
import foo

async def bar(): string:
  f = await foo()
  f_string = f"Hooray {f}! format strings are not supported in current Monarch grammar"
  return foo_string
`;
    }

    throw Error(`unsupported language: ${language}`);
}

function getTheme(themeKey: string): IRawTheme {
    return VsCodeDarkTheme;
}

window.NOJEditor = class NOJEditor {
    create(language, themeKey, elementID) {
        return main(language, themeKey, elementID);
    }
    monaco = monaco;
    editor;
}
