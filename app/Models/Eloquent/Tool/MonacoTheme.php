<?php

namespace App\Models\Eloquent\Tool;

class MonacoTheme
{
    protected static $theme=[
        "vs-dark"=>[
            "id" => "vs-dark",
            "name" => "Default",
            "background" => "rgb(30,30,30)"
        ],
        "vs"=>[
            "id" => "vs",
            "name" => "Default (White)",
            "background" => "rgb(255,255,254)"
        ],
        "hc-black"=>[
            "id" => "hc-black",
            "name" => "High Contrast (Dark)",
            "background" => "rgb(0,0,0)"
        ],
        "active4d" => [
            "id" => "active4d",
            "name" => "Active4D",
            "background" => "#FFFFFF"
        ],
        "all-hallows-eve" => [
            "id" => "all-hallows-eve",
            "name" => "All Hallows Eve",
            "background" => "#000000"
        ],
        "amy" => [
            "id" => "amy",
            "name" => "Amy",
            "background" => "#200020"
        ],
        "birds-of-paradise" => [
            "id" => "birds-of-paradise",
            "name" => "Birds of Paradise",
            "background" => "#372725"
        ],
        "blackboard" => [
            "id" => "blackboard",
            "name" => "Blackboard",
            "background" => "#0C1021"
        ],
        "brilliance-black" => [
            "id" => "brilliance-black",
            "name" => "Brilliance Black",
            "background" => "#0D0D0DFA"
        ],
        "brilliance-dull" => [
            "id" => "brilliance-dull",
            "name" => "Brilliance Dull",
            "background" => "#050505FA"
        ],
        "chrome-devtools" => [
            "id" => "chrome-devtools",
            "name" => "Chrome DevTools",
            "background" => "#FFFFFF"
        ],
        "clouds-midnight" => [
            "id" => "clouds-midnight",
            "name" => "Clouds Midnight",
            "background" => "#191919"
        ],
        "clouds" => [
            "id" => "clouds",
            "name" => "Clouds",
            "background" => "#FFFFFF"
        ],
        "cobalt" => [
            "id" => "cobalt",
            "name" => "Cobalt",
            "background" => "#002240"
        ],
        "dawn" => [
            "id" => "dawn",
            "name" => "Dawn",
            "background" => "#F9F9F9"
        ],
        "dominion-day" => [
            "id" => "dominion-day",
            "name" => "Dominion Day",
            "background" => "#372725"
        ],
        "dreamweaver" => [
            "id" => "dreamweaver",
            "name" => "Dreamweaver",
            "background" => "#FFFFFF"
        ],
        "eiffel" => [
            "id" => "eiffel",
            "name" => "Eiffel",
            "background" => "#FFFFFF"
        ],
        "espresso-libre" => [
            "id" => "espresso-libre",
            "name" => "Espresso Libre",
            "background" => "#2A211C"
        ],
        "github" => [
            "id" => "github",
            "name" => "GitHub",
            "background" => "#F8F8FF"
        ],
        "idle" => [
            "id" => "idle",
            "name" => "IDLE",
            "background" => "#FFFFFF"
        ],
        "katzenmilch" => [
            "id" => "katzenmilch",
            "name" => "Katzenmilch",
            "background" => "#E8E9E8"
        ],
        "kuroir-theme" => [
            "id" => "kuroir-theme",
            "name" => "Kuroir Theme",
            "background" => "#E8E9E8"
        ],
        "lazy" => [
            "id" => "lazy",
            "name" => "LAZY",
            "background" => "#FFFFFF"
        ],
        "magicwb--amiga-" => [
            "id" => "magicwb--amiga-",
            "name" => "MagicWB (Amiga)",
            "background" => "#969696"
        ],
        "material-design" => [
            "id" => "material-design",
            "name" => "Material Design",
            "background" => "#263238"
        ],
        "merbivore-soft" => [
            "id" => "merbivore-soft",
            "name" => "Merbivore Soft",
            "background" => "#161616"
        ],
        "merbivore" => [
            "id" => "merbivore",
            "name" => "Merbivore",
            "background" => "#161616"
        ],
        "monoindustrial" => [
            "id" => "monoindustrial",
            "name" => "MonoIndustrial",
            "background" => "#222C28"
        ],
        "monokai-bright" => [
            "id" => "monokai-bright",
            "name" => "Monokai Bright",
            "background" => "#272822"
        ],
        "monokai" => [
            "id" => "monokai",
            "name" => "Monokai",
            "background" => "#272822"
        ],
        "night-owl" => [
            "id" => "night-owl",
            "name" => "Night Owl",
            "background" => "#011627"
        ],
        "oceanic-next" => [
            "id" => "oceanic-next",
            "name" => "Oceanic Next",
            "background" => "#1B2B34"
        ],
        "pastels-on-dark" => [
            "id" => "pastels-on-dark",
            "name" => "Pastels on Dark",
            "background" => "#211E1E"
        ],
        "slush-and-poppies" => [
            "id" => "slush-and-poppies",
            "name" => "Slush and Poppies",
            "background" => "#F1F1F1"
        ],
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
        "spacecadet" => [
            "id" => "spacecadet",
            "name" => "SpaceCadet",
            "background" => "#0D0D0D"
        ],
        "sunburst" => [
            "id" => "sunburst",
            "name" => "Sunburst",
            "background" => "#000000"
        ],
        "textmate--mac-classic-" => [
            "id" => "textmate--mac-classic-",
            "name" => "Textmate (Mac Classic)",
            "background" => "#FFFFFF"
        ],
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
        "twilight" => [
            "id" => "twilight",
            "name" => "Twilight",
            "background" => "#141414"
        ],
        "upstream-sunburst" => [
            "id" => "upstream-sunburst",
            "name" => "Upstream Sunburst",
            "background" => "#000000F7"
        ],
        "vibrant-ink" => [
            "id" => "vibrant-ink",
            "name" => "Vibrant Ink",
            "background" => "#000000"
        ],
        "xcode-default" => [
            "id" => "xcode-default",
            "name" => "Xcode Default",
            "background" => "#FFFFFF"
        ],
        "zenburnesque" => [
            "id" => "zenburnesque",
            "name" => "Zenburnesque",
            "background" => "#404040"
        ],
        "iplastic" => [
            "id" => "iplastic",
            "name" => "iPlastic",
            "background" => "#EEEEEEEB"
        ],
        "idlefingers" => [
            "id" => "idlefingers",
            "name" => "idleFingers",
            "background" => "#323232"
        ],
        "krtheme" => [
            "id" => "krtheme",
            "name" => "krTheme",
            "background" => "#0B0A09"
        ],
    ];

    public static function getTheme($id) {
        if (!isset(self::$theme[$id])) {
            if(isset(self::$theme[config('app.editor_theme')])){
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
