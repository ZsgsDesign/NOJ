import type { LanguageId } from './register';
import type { ScopeName, TextMateGrammar, ScopeNameInfo } from './providers';
import * as monaco from 'monaco-editor/esm/vs/editor/editor.api';
import { createOnigScanner, createOnigString, loadWASM } from 'vscode-oniguruma';
import { NOJLanguageInfoProvider } from './providers';
import { registerLanguages } from './register';
import { rehydrateRegexps } from './configuration';
import VsCodeDarkTheme from './themes/vs-dark-plus-theme';
import VsCodeLightTheme from './themes/vs-light-plus-theme';
import { IRawTheme } from 'vscode-textmate';
import { languagesConfig } from './languages';
import { NOJThemeInfo, themes } from './themes';
import * as plist from './plist';

const loadedThemesData = require('./themeConfig.json');

interface NOJScopeNameInfo extends ScopeNameInfo {
    path: string;
}

async function main(language: LanguageId, themeKey: string, elementID: string, defaultValue: string) {
    let languagesArray = {};

    JSON.parse(languagesConfig).forEach((value) => {
        languagesArray[value.id] = value;
    });

    const languages: monaco.languages.ILanguageExtensionPoint[] = [
        {
            id: 'python',
            extensions: languagesArray['python'].extensions,
            aliases: languagesArray['python'].aliases,
            firstLine: languagesArray['python'].firstLine,
        },
        {
            id: 'c',
            extensions: languagesArray['c'].extensions,
            aliases: languagesArray['c'].aliases
        },
        {
            id: 'cpp',
            extensions: languagesArray['cpp'].extensions,
            aliases: languagesArray['cpp'].aliases
        },
        {
            id: 'cuda-cpp',
            extensions: [".cu", ".cuh"],
            aliases: ["CUDA C++"],
        },
        {
            id: 'csharp',
            extensions: languagesArray['csharp'].extensions,
            aliases: languagesArray['csharp'].aliases,
        },
        {
            id: 'kotlin',
            extensions: languagesArray['kotlin'].extensions,
            aliases: languagesArray['kotlin'].aliases,
            mimetypes: languagesArray['kotlin'].mimetypes
        },
        {
            id: 'css',
            extensions: languagesArray['css'].extensions,
            aliases: languagesArray['css'].aliases,
            mimetypes: languagesArray['css'].mimetypes
        },
        {
            id: 'html',
            extensions: languagesArray['html'].extensions,
            aliases: languagesArray['html'].aliases,
            mimetypes: languagesArray['html'].mimetypes
        },
        {
            id: 'javascript',
            extensions: languagesArray['javascript'].extensions,
            aliases: languagesArray['javascript'].aliases,
            mimetypes: languagesArray['javascript'].mimetypes,
            firstLine: languagesArray['javascript'].firstLine,
            filenames: languagesArray['javascript'].filenames,
        },
        {
            id: 'php',
            extensions: languagesArray['php'].extensions,
            aliases: languagesArray['php'].aliases,
            mimetypes: languagesArray['php'].mimetypes
        },
        {
            id: 'java',
            extensions: languagesArray['java'].extensions,
            aliases: languagesArray['java'].aliases,
            mimetypes: languagesArray['java'].mimetypes
        },
        {
            id: 'go',
            extensions: languagesArray['go'].extensions,
            aliases: languagesArray['go'].aliases,
        },
        {
            id: "haskell",
            aliases: ["Haskell", "haskell"],
            extensions: [".hsig", "hs-boot", ".hs"],
        },
        {
            id: "elixir",
            extensions: languagesArray['elixir'].extensions,
            aliases: languagesArray['elixir'].aliases,
        },
        {
            id: "ruby",
            extensions: languagesArray['ruby'].extensions,
            filenames: languagesArray['ruby'].filenames,
            aliases: languagesArray['ruby'].aliases,
        },
        {
            id: "rust",
            extensions: languagesArray['rust'].extensions,
            aliases: languagesArray['rust'].aliases,
        },
        {
            id: "swift",
            extensions: languagesArray['swift'].extensions,
            aliases: languagesArray['swift'].aliases,
            mimetypes: languagesArray['swift'].mimetypes
        },
        {
            id: "erlang",
            aliases: ["Erlang", "erlang"],
            extensions: [".erl", ".hrl", ".xrl", ".yrl", ".es", ".escript", ".app.src", "rebar.config"],
        },
        {
            id: "racket",
            extensions: [".rkt"],
            aliases: ["Racket", "racket"],
            filenames: [".rkt"],
        },
        {
            id: "scala",
            extensions: languagesArray['scala'].extensions,
            aliases: languagesArray['scala'].aliases,
            mimetypes: languagesArray['scala'].mimetypes
        },
        {
            id: "typescript",
            extensions: languagesArray['typescript'].extensions,
            aliases: languagesArray['typescript'].aliases,
            mimetypes: languagesArray['typescript'].mimetypes
        },
        {
            id: "vb",
            extensions: languagesArray['vb'].extensions,
            aliases: languagesArray['vb'].aliases,
        },
        {
            id: "pascal",
            extensions: languagesArray['pascal'].extensions,
            aliases: languagesArray['pascal'].aliases,
            mimetypes: languagesArray['pascal'].mimetypes
        },
    ];

    const grammars: { [scopeName: string]: NOJScopeNameInfo } = {
        'source.python': {
            language: 'python',
            path: 'python.tmLanguage.json',
        },
        'source.cpp.embedded.macro': {
            path: "cpp.embedded.macro.tmLanguage.json"
        },
        'source.c.platform': {
            path: "c.platform.tmLanguage.json"
        },
        'source.c': {
            language: 'c',
            path: 'c.tmLanguage.json',
        },
        'source.cpp': {
            language: 'cpp',
            path: 'cpp.tmLanguage.json',
        },
        'source.cs': {
            language: 'csharp',
            path: 'csharp.tmLanguage.json',
        },
        'source.cuda-cpp': {
            language: 'cuda-cpp',
            path: 'cuda-cpp.tmLanguage.json',
        },
        'source.kotlin': {
            language: 'kotlin',
            path: 'kotlin.tmLanguage',
        },
        'source.css': {
            language: 'css',
            path: 'css.tmLanguage.json',
        },
        'text.html.basic': {
            path: 'html.tmLanguage.json',
        },
        'text.html.derivative': {
            language: 'html',
            path: 'html-derivative.tmLanguage.json',
        },
        'source.js': {
            path: 'javascript.tmLanguage.json',
        },
        'source.js.jsx': {
            language: 'javascript',
            path: 'javascript-react.tmLanguage.json',
        },
        'source.php': {
            path: 'php.tmLanguage.json',
        },
        'text.html.php': {
            language: "php",
            path: "html-php.tmLanguage.json",
        },
        'source.java': {
            language: "java",
            path: "java.tmLanguage.json",
        },
        'source.go': {
            language: "go",
            path: "go.tmLanguage.json"
        },
        'source.haskell': {
            language: "haskell",
            path: "haskell.tmLanguage.json"
        },
        'source.elixir': {
            language: "elixir",
            path: "elixir.tmLanguage.json"
        },
        'source.ruby': {
            language: "ruby",
            path: "ruby.tmLanguage.json"
        },
        'source.rust': {
            language: "rust",
            path: "rust.tmLanguage.json"
        },
        'source.swift': {
            language: "swift",
            path: "swift.tmLanguage.json"
        },
        'source.erlang': {
            language: "erlang",
            path: "erlang.tmLanguage.json"
        },
        'source.racket': {
            language: "racket",
            path: "racket.tmLanguage.json"
        },
        'source.scala': {
            language: "scala",
            path: "scala.tmLanguage.json"
        },
        'source.ts': {
            language: "typescript",
            path: "typescript.tmLanguage.json"
        },
        'source.asp.vb.net': {
            language: "vb",
            path: "asp-vb-net.tmlanguage.json"
        },
        'source.pascal': {
            language: "pascal",
            path: "pascal.tmLanguage"
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

    let themeData = await getTheme(themeKey);

    const provider = new NOJLanguageInfoProvider({
        grammars,
        fetchGrammar,
        configurations: languages.map((language) => language.id),
        fetchConfiguration,
        theme: themeData,
        onigLib,
        monaco,
    });

    registerLanguages(
        languages,
        (language: LanguageId) => provider.fetchLanguageInfo(language),
        monaco,
    );

    let themes = Object.keys(loadedThemesData);
    themes.forEach(theme => {
        monaco.editor.defineTheme(theme, loadedThemesData[theme]);
    });

    const value = defaultValue;
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
        autoDetectHighContrast: false,
        minimap: {
            enabled: true,
        },
    });

    provider.injectCSS();

    return [editor, provider];
}

async function loadVSCodeOnigurumWASM(): Promise<Response | ArrayBuffer> {
    const response = await fetch('/static/library/vscode-oniguruma/release/onig.wasm');
    const contentType = response.headers.get('content-type');
    if (contentType === 'application/wasm') {
        return response;
    }

    return await response.arrayBuffer();
}

const fetchTheme = async (themeID: string): Promise<IRawTheme> => {
    const { path } = themes[themeID];
    const uri = `/static/language-services/themes/${path}`;
    const response = await fetch(uri);
    const themeData = await response.text();
    if (path.endsWith('.json')) {
        return JSON.parse(themeData);
    }
    return plist.parse(themeData);
};

const getTheme = async (themeID: string) => {
    let themeData;

    if(themeID === 'vs-dark') {
        themeData = VsCodeDarkTheme;
    }
    else if(themeID === 'vs') {
        themeData = VsCodeLightTheme;
    }
    else {
        themeData = await fetchTheme(themeID);
    }

    if ('tokenColors' in themeData) {
        themeData.settings = themeData.tokenColors;
    }

    return themeData;
}

const changeTheme = (provider: NOJLanguageInfoProvider, themeID: string) => {
    Promise.resolve(getTheme(themeID)).then((themeData) => {
        provider.setTheme(themeData);
    });
}

window.NOJEditor = class NOJEditor {
    create(language, themeKey, elementID, defaultValue) {
        return main(language, themeKey, elementID, defaultValue);
    }
    changeTheme(provider: NOJLanguageInfoProvider, themeID: string) {
        changeTheme(provider, themeID);
    }
    monaco = monaco;
    editor;
}
