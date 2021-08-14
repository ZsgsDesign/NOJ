<?php

return [

    'subject' => [
        'fullName' => env('TERM_SUBJECT_FULL_NAME', env('APP_FULL_NAME', 'NJUPT Online Judge')),
        'name' => env("TERM_SUBJECT_NAME", env('APP_NAME', 'NOJ')),
    ],

    'street' => env("TERM_STREET", '9 Wenyuan Road'),
    'city' => env("TERM_CITY", 'Nanjing'),
    'province' => env("TERM_PROVINCE", 'Jiangsu'),
    'state' => env("TERM_STATE", 'China'),
    'zip' => env("TERM_ZIP", '221000'),

    'contact' => [
        'email' => env("TERM_CONTACT_EMAIL", 'noj@njupt.edu.cn')
    ],
];
