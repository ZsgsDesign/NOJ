<?php

$contestLocale = config('hasaaose.enable') ? '考试' : '比赛';

return [
    'home'          => '主页',
    'problem'       => '题库',
    'problems' => [
        'description' => '描述',
        'article'     => '讨论',
        'solution'    => '题解',
        'editor'      => '编辑器',
    ],
    'dojo'          => '训练场',
    'status'        => '状态',
    'rank'          => '排行榜',
    'contest'       => $contestLocale,
    'group'         => '群组',
    'search'        => '站内搜索',
    'dashboard'     => '个人主页',
    'settings'      => '设置',
    'admin'         => '管理面板',
    'pastebin'      => '剪切板',
    'imagehosting'  => '图片托管',
    'systeminfo'    => '系统信息',
    'report'        => '反馈BUG',
    'logout'        => '注销',
    'account'       => '登录/注册',
    'emailverify'   => '<strong>注意您的账号安全!</strong> 您还没有验证您的邮箱，请在个人设置界面进行邮箱验证。',
    'terms'         => '使用条款',
    'message' => [
        'empty'     => '您没有新的消息。',
        'tip_head'  => '您有 ',
        'tip_foot'  => ' 条未读消息。',
        'center'    => '消息中心',
    ],
    'greeting' => [
        'morning'   => '早上好',
        'afternoon' => '下午好',
        'evening'   => '傍晚好',
        'night'     => '晚上好',
        'bed'       => '夜深了'
    ]
];
