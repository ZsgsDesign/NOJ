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
};

export { themes };
