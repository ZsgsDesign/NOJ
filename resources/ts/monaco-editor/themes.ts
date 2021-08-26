export type NOJThemeInfo = {
    name: string;
    path: string;
};

const themes: { [themeID: string]: NOJThemeInfo } = {
    'hc-black': {
        name: 'High Contrast (Dark)',
        path: 'hc_black.json',
    },
    'kimbie-dark': {
        name: 'Kimbie Dark',
        path: 'kimbie-dark.json',
    },
    'monokai-classic': {
        name: 'Monokai Classic',
        path: 'monokai-classic.json',
    },
    'monokai-pro': {
        name: 'Monokai Pro',
        path: 'monokai-pro.json',
    },
    'material-design': {
        name: 'Material Design',
        path: 'material.json',
    },
    'material-design-darker': {
        name: 'Material Design Darker',
        path: 'material_dark.json',
    },
    'material-design-lighter': {
        name: 'Material Design Lighter',
        path: 'material_light.json',
    },
    'github-dark': {
        name: 'Github Dark',
        path: 'github-dark-default.json',
    },
    'github': {
        name: 'Github Light',
        path: 'github-light-default.json',
    },
    'solarized-light': {
        name: 'Solarized Light',
        path: 'solarized-light.json',
    },
    'solarized-dark': {
        name: 'Solarized Dark',
        path: 'solarized-dark.json',
    },
    'abyss': {
        name: 'Abyss',
        path: 'abyss.json',
    },
    'quietlight': {
        name: 'Quiet Light',
        path: 'quietlight.json',
    },
    'red': {
        name: 'Red',
        path: 'red.json',
    },
    'tomorrow-night-blue': {
        name: 'Tomorrow Night Blue',
        path: 'tomorrow-night-blue.json',
    },
    'tomorrow-night-bright': {
        name: 'Tomorrow Night Bright',
        path: 'tomorrow-night-bright.json',
    },
    'tomorrow-night-eighties': {
        name: 'Tomorrow Night Eighties',
        path: 'tomorrow-night-eighties.json',
    },
    'tomorrow-night': {
        name: 'Tomorrow Night',
        path: 'tomorrow-night.json',
    },
    'tomorrow': {
        name: 'Tomorrow',
        path: 'tomorrow.json',
    },
    'onehalf-dark': {
        name: 'One Half Dark',
        path: 'onehalf-dark.json',
    },
    'onehalf-light': {
        name: 'One Half Light',
        path: 'onehalf-light.json',
    },
    'winter-is-coming': {
        name: 'Winter is Coming',
        path: 'winter-is-coming.json',
    },
    'dracula': {
        name: 'Dracula',
        path: 'dracula.json',
    },
    'synthwave': {
        name: 'SynthWave 84',
        path: 'synthwave.json',
    },
    'chrome-dev-tools': {
        name: 'Chrome DevTools',
        path: 'chrome-dev-tools.json',
    },
};

export { themes };
