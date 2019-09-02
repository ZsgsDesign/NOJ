<?php

return [

    'subject' => [
        'fullName' => env('TERM_SUBJECT_FULL_NAME', env('APP_FULL_NAME', 'NJUPT Online Judge')),
        'name' => env("TERM_SUBJECT_NAME", env('APP_NAME', 'NOJ')),
    ],

    'street' => env("TERM_STREET", '9 Wenyuan Road'),
    'city' => env("TERM_STREET", 'Nanjing'),
    'province' => env("TERM_STREET", 'Jiangsu'),
    'state' => env("TERM_STREET", 'China'),
    'zip' => env("TERM_ZIP", '221000'),

    'contact' => [
        'email' => env("TERM_CONTACT_EMAIL", 'noj@njupt.edu.cn')
    ],
];
