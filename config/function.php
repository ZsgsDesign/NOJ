<?php

return [
    'register' => env('FUNC_ENABLE_REGISTER', true),
    'password' => [
        'strong' => env('FUNC_STRONG_PASSWORD', false),
    ]
];
