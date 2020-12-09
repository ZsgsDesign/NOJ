<?php

return [
    'empty'                 => '暂无比赛。',
    'featured' => [
        'title'             => '推荐比赛',
        'action'            => '了解更多',
        'empty'             => '暂无推荐。',
    ],
    'filter' => [
        'title'             => '筛选器',
        'icpc'              => 'ICPC',
        'ioi'               => 'IOI',
        'public'            => '公开比赛',
        'private'           => '私有比赛',
        'verified'          => '认证比赛',
        'practice'          => '训练赛',
        'rated'             => '评级比赛',
        'anticheated'       => '反作弊',
    ],
    'badge' => [
        'desktop'           => '支持 NOJ Desktop 桌面客户端',
        'audit'             => '这场比赛正在审核中',
        'private'           => '这是一个私有比赛',
        'verified'          => '这是一场认证比赛',
        'practice'          => '这是一场训练赛',
        'rated'             => '这场比赛将会评级',
        'anticheated'       => '这场比赛将会进行反作弊检查',
    ],
    'lengthformatter' => [
        'seconds'           => '{0} :count 秒',
        'minutes'           => '{0} :count 分钟',
        'hours'             => '{0} :count 小时',
    ],
    'infobar' => [
        'begin'             => '开始时间',
        'length'            => '比赛时长',
        'problems'          => '题目数量',
        'organizer'         => '比赛组织',
        'action' => [
            'login'         => '请先登录',
            'review'        => '审核中',
            'manage'        => '管理',
            'registered'    => '已报名',
            'forbidden'     => '无权限',
            'regist'        => '报名',
            'notstarted'    => '尚未开始',
            'desktoponly'   => '仅限客户端',
            'enter'         => '进入比赛',
        ],
    ],
    'inside' => [
        'topbar' => [
            'challenge'     => '试题集',
            'rank'          => '比赛榜单',
            'status'        => '提交状态',
            'clarification' => '答疑服务',
            'print'         => '打印服务',
            'analysis'      => '分析服务',
            'admin'         => '管理面板',
        ],
        'counter' => [
            'end'         => '比赛已结束',
            'run'         => '比赛进行中',
        ],
        'challenge' => [

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
                'account'       => '比赛账号生成',
                'announce'      => '发布比赛公告',
                'manage'        => '比赛管理',
                'refreshrank'   => '刷新比赛榜单',
                'download'      => '下载选手代码',
                'scrollboard'   => '滚榜（测试版）',
            ],
            'account' => [
                'prefix'        => '账号前缀',
                'count'         => '生成数量',
                'generate'      => '开始生成',
                'download'      => '下载为XLSX格式',
                'field' => [
                    'name'      => '用户名',
                    'account'   => '登录账号',
                    'password'  => '登录密码',
                ],
            ],
        ],
    ],
];
