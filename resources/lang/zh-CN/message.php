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
            'desc'  => "嗨，**:receiverName**：\n\n您为如下题目提交的题解已经通过了：\n\n :problemList\n\n 此致，:siteName",
        ],
        'declined' => [
            'title' => '您提交的题解被拒绝了',
            'desc'  => "嗨，**:receiverName**：\n\n您为如下题目提交的题解被拒绝了：\n\n :problemList\n\n 此致，:siteName",
        ],
    ],
    'group' => [
        'agreed' => [
            'title' => '他们已同意加入您的群组 :name',
            'desc'  => "嗨，**:receiverName**：\n\n如下用户已经同意加入您的群组 :groupInfo ：\n\n :userList\n\n您可在群组页管理群成员。\n\n 此致，:siteName"
        ],
        'applied' => [
            'title' => '有人申请加入您的群组 :name',
            'desc'  => "嗨，**:receiverName**：\n\n如下用户申请加入您的群组 :groupInfo ：\n\n :userList\n\n您可在群组页管理群成员。\n\n 此致，:siteName"
        ],
        'invited' => [
            'title' => ':senderName 邀请您加入 :groupName',
            'desc'  => "嗨，**:receiverName**：\n\n**:senderName** 刚刚邀请您加入 **:groupInfo** 群组。现在就看一看，认识一些有趣的人吧！\n\n 此致，:siteName"
        ]
    ],
    'homework' => [
        'new' => [
            'title' => '您有一项或多项作业',
            'desc'  => "嗨，**:receiverName**：\n\n以下是您所参加的群组中由管理员或群主发布的任务，请您查阅：\n\n :homeworkList\n\n您可以在任何时间完成它们，只要没有越过截止日期。\n\n 此致，:siteName"
        ]
    ],
    'rank' => [
        'up' => [
            'title' => '自从上次登录以来您的全局排名提高了',
            'desc'  => "嗨，**:receiverName**：\n\n自从上次登录后，您的全球排名已经经过了重新计算，从原来的排名上升到现在的排名 **:currentRank** 。\n\n继续加油！我们为你在这一过程中的收获感到高兴。\n\n 此致，:siteName"
        ],
        'down' => [
            'title' => '自从上次登录以来您的全局排名下降了',
            'desc'  => "嗨，**:receiverName**：\n\n自从上次登录后，您的全球排名已经经过了重新计算，从原来的排名 **:originalRank** 下降到现在的排名 **:currentRank** 。\n\n 不要伤心，我们等待着你的荣耀归来！\n\n 此致，:siteName"
        ]
    ],
];
