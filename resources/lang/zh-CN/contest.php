<?php

$contestLocale = config('hasaaose.enable') ? '考试' : '比赛';
$participantLocale = config('hasaaose.enable') ? '考生' : '选手';
$featureLocale = config('hasaaose.enable') ? '重点' : '推荐';

return [
    'empty'                 => "暂无{$contestLocale}。",
    'featured' => [
        'title'             => "{$featureLocale}{$contestLocale}",
        'action'            => '了解更多',
        'empty'             => "暂无{$featureLocale}。",
    ],
    'filter' => [
        'title'             => '筛选器',
        'icpc'              => 'ICPC',
        'ioi'               => 'IOI',
        'public'            => "公开$contestLocale",
        'private'           => "私有$contestLocale",
        'verified'          => "认证$contestLocale",
        'practice'          => '训练赛',
        'rated'             => "评级$contestLocale",
        'anticheated'       => '反作弊',
    ],
    'badge' => [
        'desktop'           => '支持 NOJ Desktop 桌面客户端',
        'audit'             => "这场{$contestLocale}正在审核中",
        'private'           => "这是一个私有$contestLocale",
        'verified'          => "这是一场认证$contestLocale",
        'practice'          => '这是一场训练赛',
        'rated'             => "这场{$contestLocale}将会评级",
        'anticheated'       => "这场{$contestLocale}将会进行反作弊检查",
    ],
    'desktop' => [
        'product'           => 'NOJ Desktop 桌面客户端',
        'desc'              => '本场' . $contestLocale . '使用 <strong>NOJ Desktop 桌面客户端</strong>，一款功能强大的OI/ICPC竞赛跨平台客户端，支持 <i class="MDI windows"></i> Windows、<i class="MDI apple"></i> MacOS 和 <i class="MDI ubuntu"></i> Ubuntu。',
        'download'          => '下载',
    ],
    'lengthformatter' => [
        'seconds'           => '{0} :count 秒',
        'minutes'           => '{0} :count 分钟',
        'hours'             => '{0} :count 小时',
    ],
    'infobar' => [
        'begin'             => '开始时间',
        'length'            => "{$contestLocale}时长",
        'problems'          => '题目数量',
        'organizer'         => "{$contestLocale}组织",
        'action' => [
            'login'         => '请先登录',
            'review'        => '审核中',
            'manage'        => '管理',
            'registered'    => '已报名',
            'forbidden'     => '无权限',
            'regist'        => '报名',
            'notstarted'    => '尚未开始',
            'desktoponly'   => '仅限客户端',
            'enter'         => "进入{$contestLocale}",
        ],
    ],
    'inside' => [
        'topbar' => [
            'challenge'     => '试题集',
            'rank'          => "{$contestLocale}榜单",
            'status'        => '提交状态',
            'clarification' => '答疑服务',
            'print'         => '打印服务',
            'analysis'      => '分析服务',
            'admin'         => '管理面板',
        ],
        'counter' => [
            'end'         => "{$contestLocale}已结束",
            'run'         => "{$contestLocale}进行中",
        ],
        'challenge' => [
            'title'       => '题目:ncode',
        ],
        'rank' => [
            'title'       => '排名',
            'account'     => '用户',
            'score'       => '得分',
            'penalty'     => '罚时',
            'solved'      => '解题',
        ],
        'clarification' => [
            'seemore'     => '查看更多',
            'clarification'   => '答疑',
            'announcement'    => '公告',
            'request'     => '请求释疑',
            'issue'       => '发布公告',
            'action' => [
                'public'  => '公开',
                'reply'   => '回复',
                'close'   => '关闭',
                'issue'   => '发布',
                'request' => '发送',
            ],
            'field' => [
                'title'   => '标题',
                'content' => '内容',
            ],
        ],
        'print' => [
            'disable'     => '暂不支持。',
        ],
        'analysis' => [
            'member'      => '成员',
        ],
        'admin' => [
            'nav' => [
                'account'       => "{$contestLocale}账号生成",
                'announce'      => "发布{$contestLocale}公告",
                'manage'        => "{$contestLocale}管理",
                'pdf'           => '生成PDF试题集',
                'anticheat'     => '反作弊',
                'rejudge'       => '代码重测',
                'refreshrank'   => "刷新{$contestLocale}榜单",
                'download'      => "下载{$participantLocale}代码",
                'scrollboard'   => '滚榜',
            ],
            'account' => [
                'prefix'        => '账号前缀',
                'domain'        => '账号域',
                'count'         => '生成数量',
                'file'          => '导入名单Excel以生成账号',
                'generate'      => '开始生成',
                'generating'    => '生成中',
                'generated'     => '生成成功',
                'failed'        => '生成失败',
                'download'      => '下载为XLSX格式',
                'field' => [
                    'name'      => '用户名',
                    'account'   => '登录账号',
                    'password'  => '登录密码',
                ],
            ],
            'anticheat' => [
                'run'           => '运行代码查重检测',
                'rerun'         => '重新运行',
                'running'       => '代码查重检测运行中',
                'failed'        => '代码查重检测运行失败',
                'download'      => '下载代码查重检测报告',
                'alert'         => '代码查重检测正在后台运行，请稍后访问本页面获取结果。',
                'downloadFile'  => ':name 代码查重检测报告',
            ],
            'scrollboard' => [
                'guide' => [
                    'title'     => '快速指南',
                    'content'   => '<p>请按下 <kbd>Ctrl</kbd> + <kbd>Enter</kbd> 以开启自动滚榜。</p><p>如果您想要完全控制，每次按下 <kbd>Enter</kbd> 步进榜单。</p>',
                    'no'        => '关闭',
                    'yes'       => '确定',
                ],
                'gold'          => '金牌数量',
                'silver'        => '银牌数量',
                'bronze'        => '铜牌数量',
                'confirm'       => '确认',
                'submits'       => '提交',
            ],
        ],
    ],
];
