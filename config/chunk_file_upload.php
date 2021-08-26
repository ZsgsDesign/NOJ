<?php

return [
    'disks' => [
        'temp' => [
            'driver' => 'local',
            'root' => storage_path('temp'),
            'visibility' => 'private',
            'url' => 'fake.path',
        ],
    ],
    'default' => [
        'disk' => 'temp',
        'extensions' => 'zip',
        'mimeTypes' => 'application/zip',
        'fileSizeLimit' => 209715200,
        'fileNumLimit' => '1',
        'saveType' => 'json',
    ]
];
