<?php

return [
    'messagelist'   => '消息盒子',
    'markAllAsRead' => '全部标为已读',
    'eraseRead'     => '删除已读',
    'empty'         => '空落落的，什么也没有。',
    'official'      => ':name官方',
    'delimiter'     => '，',
    'solution' => [
        'accepted' => [
            'title' => '您提交的题解已经通过了',
            'desc' => "您为如下题目提交的题解已经通过了：\n\n :problemList",
        ],
        'declined' => [
            'title' => '您提交的题解被拒绝了',
            'desc' => "您为如下题目提交的题解被拒绝了：\n\n :problemList",
        ],
    ],
    'group' => [
        'agreed' => [
            'title'   => '他们已同意加入您的群组 :name',
            'desc' => "如下用户已经同意加入您的群组 :groupInfo ：\n\n :userList\n\n您可在群组页管理群成员。"
        ],
        'applied' => [
            'title'   => '有人申请加入您的群组 :name',
            'desc' => "如下用户申请加入您的群组 :groupInfo ：\n\n :userList\n\n您可在群组页管理群成员。"
        ],
        'invited' => [
            'title'   => ':sender_name 邀请您加入 :group_name',
            'desc' => "嗨,  **:reciver_name**,\n\n  **:sender_name** 刚刚邀请您加入 **[:group_name](:group_url)** 群组. 现在就看一看，认识一些有趣的人吧!\n\n NOJ"
        ]
    ],
];
