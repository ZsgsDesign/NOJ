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
        path: 'kimbie-dark-color-theme.json',
    },
    'monokai': {
        name: 'Monokai',
        path: 'Monokai.tmTheme',
    },
    'material-design': {
        name: 'Material Design',
        path: 'Material-Theme.tmTheme',
    },
};

export { themes };
