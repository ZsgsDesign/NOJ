<?php

namespace App\Models\Eloquent\Tool;

class Theme
{
    protected static $theme=[
        "default"=>[
            'name'=>'Default',
            'primaryColor'=>'#3E4551',
        ],
        "classic"=>[
            'name'=>'Classic',
            'primaryColor'=>'#424242',
        ],
        "cranberry"=>[
            'name'=>'Cranberry',
            'primaryColor'=>'#C62828',
        ],
        "byzantium"=>[
            'name'=>'Byzantium',
            'primaryColor'=>'#ad1457',
        ],
        "orchids"=>[
            'name'=>'Orchids',
            'primaryColor'=>'#6a1b9a',
        ],
        "blueberry"=>[
            'name'=>'Blueberry',
            'primaryColor'=>'#4527a0',
        ],
        "starrynights"=>[
            'name'=>'Starrynights',
            'primaryColor'=>'#283593',
        ],
        "electric"=>[
            'name'=>'Electric',
            'primaryColor'=>'#1565C0',
        ],
        "oceanic"=>[
            'name'=>'Oceanic',
            'primaryColor'=>'#0277bd',
        ],
        "emerald"=>[
            'name'=>'Emerald',
            'primaryColor'=>'#00695c',
        ],
        "aventurine"=>[
            'name'=>'Aventurine',
            'primaryColor'=>'#2E7D32',
        ],
        "tropical"=>[
            'name'=>'Tropical',
            'primaryColor'=>'#ef6c00',
        ],
        "ginger"=>[
            'name'=>'Ginger',
            'primaryColor'=>'#d84315',
        ],
        "espresso"=>[
            'name'=>'Espresso',
            'primaryColor'=>'#4e342e',
        ],
        "enigma"=>[
            'name'=>'Enigma',
            'primaryColor'=>'#37474f',
        ],
    ];

    public static function getTheme($id) {
        if (!isset(self::$theme[$id])) {
            return self::$theme['default'];
        }
        return self::$theme[$id];
    }

    public static function getAll() {
        return self::$theme;
    }
}
