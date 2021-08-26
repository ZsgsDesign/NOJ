<?php

namespace App\Models\Eloquent\Tool;

class MonacoTheme
{
    protected static $theme=[
        "material-design-darker"=>[
            "id" => "material-design-darker",
            "name" => "Default",
            "background" => "#212121"
        ],
        "material-design-lighter"=>[
            "id" => "material-design-lighter",
            "name" => "Default (White)",
            "background" => "#FAFAFA"
        ],
        "hc-black"=>[
            "id" => "hc-black",
            "name" => "Default (High Contrast)",
            "background" => "#000000"
        ],
        "abyss" => [
            "id" => "abyss",
            "name" => "Abyss",
            "background" => "#000c18"
        ],
        // "active4d" => [
        //     "id" => "active4d",
        //     "name" => "Active4D",
        //     "background" => "#FFFFFF"
        // ],
        // "all-hallows-eve" => [
        //     "id" => "all-hallows-eve",
        //     "name" => "All Hallows Eve",
        //     "background" => "#000000"
        // ],
        // "amy" => [
        //     "id" => "amy",
        //     "name" => "Amy",
        //     "background" => "#200020"
        // ],
        // "birds-of-paradise" => [
        //     "id" => "birds-of-paradise",
        //     "name" => "Birds of Paradise",
        //     "background" => "#372725"
        // ],
        // "behave" => [
        //     "id" => "behave",
        //     "name" => "Behave",
        //     "background" => "#2C333D"
        // ],
        // "blackboard" => [
        //     "id" => "blackboard",
        //     "name" => "Blackboard",
        //     "background" => "#0C1021"
        // ],
        // "brilliance-black" => [
        //     "id" => "brilliance-black",
        //     "name" => "Brilliance Black",
        //     "background" => "#0D0D0DFA"
        // ],
        // "brilliance-dull" => [
        //     "id" => "brilliance-dull",
        //     "name" => "Brilliance Dull",
        //     "background" => "#050505FA"
        // ],
        "chrome-dev-tools" => [
            "id" => "chrome-dev-tools",
            "name" => "Chrome DevTools",
            "background" => "#fff"
        ],
        // "clouds-midnight" => [
        //     "id" => "clouds-midnight",
        //     "name" => "Clouds Midnight",
        //     "background" => "#191919"
        // ],
        // "clouds" => [
        //     "id" => "clouds",
        //     "name" => "Clouds",
        //     "background" => "#FFFFFF"
        // ],
        // "cobalt" => [
        //     "id" => "cobalt",
        //     "name" => "Cobalt",
        //     "background" => "#002240"
        // ],
        // "dawn" => [
        //     "id" => "dawn",
        //     "name" => "Dawn",
        //     "background" => "#F9F9F9"
        // ],
        "dracula" => [
            "id" => "dracula",
            "name" => "Dracula",
            "background" => "#282A36"
        ],
        // "dominion-day" => [
        //     "id" => "dominion-day",
        //     "name" => "Dominion Day",
        //     "background" => "#372725"
        // ],
        // "dreamweaver" => [
        //     "id" => "dreamweaver",
        //     "name" => "Dreamweaver",
        //     "background" => "#FFFFFF"
        // ],
        // "eiffel" => [
        //     "id" => "eiffel",
        //     "name" => "Eiffel",
        //     "background" => "#FFFFFF"
        // ],
        // "espresso-libre" => [
        //     "id" => "espresso-libre",
        //     "name" => "Espresso Libre",
        //     "background" => "#2A211C"
        // ],
        "github" => [
            "id" => "github",
            "name" => "GitHub",
            "background" => "#ffffff"
        ],
        "github-dark" => [
            "id" => "github-dark",
            "name" => "GitHub Dark",
            "background" => "#0d1117"
        ],
        // "idle" => [
        //     "id" => "idle",
        //     "name" => "IDLE",
        //     "background" => "#FFFFFF"
        // ],
        // "katzenmilch" => [
        //     "id" => "katzenmilch",
        //     "name" => "Katzenmilch",
        //     "background" => "#E8E9E8"
        // ],
        "kimbie-dark"=>[
            "id" => "kimbie-dark",
            "name" => "Kimbie Dark",
            "background" => "#221a0f"
        ],
        // "kuroir-theme" => [
        //     "id" => "kuroir-theme",
        //     "name" => "Kuroir Theme",
        //     "background" => "#E8E9E8"
        // ],
        // "lazy" => [
        //     "id" => "lazy",
        //     "name" => "LAZY",
        //     "background" => "#FFFFFF"
        // ],
        // "magicwb--amiga-" => [
        //     "id" => "magicwb--amiga-",
        //     "name" => "MagicWB (Amiga)",
        //     "background" => "#969696"
        // ],
        "material-design" => [
            "id" => "material-design",
            "name" => "Material Design",
            "background" => "#263238"
        ],
        // "merbivore-soft" => [
        //     "id" => "merbivore-soft",
        //     "name" => "Merbivore Soft",
        //     "background" => "#161616"
        // ],
        // "merbivore" => [
        //     "id" => "merbivore",
        //     "name" => "Merbivore",
        //     "background" => "#161616"
        // ],
        // "monoindustrial" => [
        //     "id" => "monoindustrial",
        //     "name" => "MonoIndustrial",
        //     "background" => "#222C28"
        // ],
        "monokai-pro" => [
            "id" => "monokai-pro",
            "name" => "Monokai Pro",
            "background" => "#2d2a2e"
        ],
        "monokai-classic" => [
            "id" => "monokai-classic",
            "name" => "Monokai Classic",
            "background" => "#272822"
        ],
        // "night-owl" => [
        //     "id" => "night-owl",
        //     "name" => "Night Owl",
        //     "background" => "#011627"
        // ],
        // "oceanic-next" => [
        //     "id" => "oceanic-next",
        //     "name" => "Oceanic Next",
        //     "background" => "#1B2B34"
        // ],
        "onehalf-dark"=>[
            "id" => "onehalf-dark",
            "name" => "One Half Dark",
            "background" => "#282c34"
        ],
        "onehalf-light"=>[
            "id" => "onehalf-light",
            "name" => "One Half Light",
            "background" => "#fafafa"
        ],
        // "pastels-on-dark" => [
        //     "id" => "pastels-on-dark",
        //     "name" => "Pastels on Dark",
        //     "background" => "#211E1E"
        // ],
        "quietlight" => [
            "id" => "quietlight",
            "name" => "Quiet Light",
            "background" => "#F5F5F5"
        ],
        "red" => [
            "id" => "red",
            "name" => "Red",
            "background" => "#390000"
        ],
        // "slush-and-poppies" => [
        //     "id" => "slush-and-poppies",
        //     "name" => "Slush and Poppies",
        //     "background" => "#F1F1F1"
        // ],
        "solarized-dark" => [
            "id" => "solarized-dark",
            "name" => "Solarized Dark",
            "background" => "#002B36"
        ],
        "solarized-light" => [
            "id" => "solarized-light",
            "name" => "Solarized Light",
            "background" => "#FDF6E3"
        ],
        // "spacecadet" => [
        //     "id" => "spacecadet",
        //     "name" => "SpaceCadet",
        //     "background" => "#0D0D0D"
        // ],
        // "sunburst" => [
        //     "id" => "sunburst",
        //     "name" => "Sunburst",
        //     "background" => "#000000"
        // ],
        "synthwave" => [
            "id" => "synthwave",
            "name" => "SynthWave 84",
            "background" => "#262335"
        ],
        // "textmate--mac-classic-" => [
        //     "id" => "textmate--mac-classic-",
        //     "name" => "Textmate (Mac Classic)",
        //     "background" => "#FFFFFF"
        // ],
        "tomorrow-night-blue" => [
            "id" => "tomorrow-night-blue",
            "name" => "Tomorrow Night Blue",
            "background" => "#002451"
        ],
        "tomorrow-night-bright" => [
            "id" => "tomorrow-night-bright",
            "name" => "Tomorrow Night Bright",
            "background" => "#000000"
        ],
        "tomorrow-night-eighties" => [
            "id" => "tomorrow-night-eighties",
            "name" => "Tomorrow Night Eighties",
            "background" => "#2D2D2D"
        ],
        "tomorrow-night" => [
            "id" => "tomorrow-night",
            "name" => "Tomorrow Night",
            "background" => "#1D1F21"
        ],
        "tomorrow" => [
            "id" => "tomorrow",
            "name" => "Tomorrow",
            "background" => "#FFFFFF"
        ],
        // "twilight" => [
        //     "id" => "twilight",
        //     "name" => "Twilight",
        //     "background" => "#141414"
        // ],
        // "upstream-sunburst" => [
        //     "id" => "upstream-sunburst",
        //     "name" => "Upstream Sunburst",
        //     "background" => "#000000F7"
        // ],
        // "vibrant-ink" => [
        //     "id" => "vibrant-ink",
        //     "name" => "Vibrant Ink",
        //     "background" => "#000000"
        // ],
        "vs-dark"=>[
            "id" => "vs-dark",
            "name" => "Visual Studio",
            "background" => "rgb(30,30,30)"
        ],
        "vs"=>[
            "id" => "vs",
            "name" => "Visual Studio (White)",
            "background" => "rgb(255,255,254)"
        ],
        "winter-is-coming"=>[
            "id" => "winter-is-coming",
            "name" => "Winter is Coming",
            "background" => "#011627"
        ],
        // "xcode-default" => [
        //     "id" => "xcode-default",
        //     "name" => "Xcode Default",
        //     "background" => "#FFFFFF"
        // ],
        // "zenburnesque" => [
        //     "id" => "zenburnesque",
        //     "name" => "Zenburnesque",
        //     "background" => "#404040"
        // ],
        // "iplastic" => [
        //     "id" => "iplastic",
        //     "name" => "iPlastic",
        //     "background" => "#EEEEEEEB"
        // ],
        // "idlefingers" => [
        //     "id" => "idlefingers",
        //     "name" => "idleFingers",
        //     "background" => "#323232"
        // ],
        // "krtheme" => [
        //     "id" => "krtheme",
        //     "name" => "krTheme",
        //     "background" => "#0B0A09"
        // ],
    ];

    public static function getTheme($id) {
        if (!isset(self::$theme[$id])) {
            if (isset(self::$theme[config('app.editor_theme')])) {
                return self::$theme[config('app.editor_theme')];
            }
            return self::$theme['material-design-darker'];
        }
        return self::$theme[$id];
    }

    public static function getAll() {
        return self::$theme;
    }
}
