<?php

return [
    'tips'                  => "我们只知道这些。",
    'http' => [
        '403' => [
            'type'          => 'Access Forbidden',
            'description'   => '对服务器上该资源的访问被拒绝！',
        ],
        '404' => [
            'type'          => 'Not Found',
            'description'   => '请求的资源URL在服务器上不可寻。',
        ],
        '419' => [
            'type'          => 'Session Expired',
            'description'   => '请刷新此页面！',
        ],
        '422' => [
            'type'          => 'Unprocessable Entity',
        ],
        '451' => [
            'type'          => 'Unavailable For Legal Reasons',
            'description'   => '对服务器上该资源的访问被拒绝！',
        ],
        '500' => [
            'type'          => 'Internal Server Error',
            'description'   => '服务器遇到了不知道如何处理的情况。',
            'tips'          => "我们正在查找该错误的原因"
        ],
        '503' => [
            'type'          => 'Maintenance Mode',
            'description'   => ':name 当前正在升级或维护中',
            'tips'          => '请稍候再行访问'
        ]
    ],
    'alphacome' => [
        'illegal' => [
            'desc'          => "这里不可以落子！",
            'title'         => 'AlphaCome'
        ],
        'win' => [
            'desc'          => 'AlphaCome获胜了！',
            'title'         => 'AlphaCome：人工智能'
        ],
        'lose' => [
            'desc'          => '玩家获胜了！',
            'title'         => 'AlphaCome：人工滞能'
        ],
        'restart'           => "重来一局",
        'notsupported'      => "您的浏览器不支持AlphaCome。",
    ],
    'default'               => '服务器连接错误',
];
