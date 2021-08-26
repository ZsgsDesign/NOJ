import type * as monaco from 'monaco-editor';

/**
 * Fields that, if present in a LanguageConfiguration, must be a RegExp object
 * rather than a string literal.
 */
const REGEXP_PROPERTIES = [
    // indentation
    'indentationRules.decreaseIndentPattern',
    'indentationRules.increaseIndentPattern',
    'indentationRules.indentNextLinePattern',
    'indentationRules.unIndentedLinePattern',

    // code folding
    'folding.markers.start',
    'folding.markers.end',

    // language's "word definition"
    'wordPattern',
];

export function rehydrateRegexps(rawConfiguration: string): monaco.languages.LanguageConfiguration {
    const out = JSON.parse(rawConfiguration);
    for (const property of REGEXP_PROPERTIES) {
        const value = getProp(out, property);
        if (typeof value === 'string') {
            setProp(out, property, new RegExp(value));
        }
    }
    return out;
}

function getProp(obj: { string: any }, selector: string): any {
    const components = selector.split('.');
    // @ts-ignore
    return components.reduce((acc, cur) => (acc != null ? acc[cur] : null), obj);
}

function setProp(obj: { string: any }, selector: string, value: RegExp): void {
    const components = selector.split('.');
    const indexToSet = components.length - 1;
    components.reduce((acc, cur, index) => {
        if (acc == null) {
            return acc;
        }

        if (index === indexToSet) {
            // @ts-ignore
            acc[cur] = value;
            return null;
        } else {
            // @ts-ignore
            return acc[cur];
        }
    }, obj);
}
