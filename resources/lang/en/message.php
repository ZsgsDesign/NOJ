<?php

return [
    'messagelist'   => 'Message List',
    'markAllAsRead' => 'Mark all as read',
    'eraseRead'     => 'Erase read',
    'empty'         => 'You have no message.',
    'official'      => ':name Official',
    'delimiter'     => ', ',
    'solution' => [
        'accepted' => [
            'title' => 'Some of your solutions have been accepted',
            'desc'  => "Your submitted solutions to the following problems have been accepted: \n\n :problemList",
        ],
        'declined' => [
            'title' => 'Some of your solutions have been declined',
            'desc'  => "Your submitted solutions to the following problems have been declined: \n\n :problemList",
        ],
    ],
    'group' => [
        'agreed' => [
            'title' => 'Some users agreed to join your group :name',
            'desc'  => "The following users agreed to join your group :groupInfo :\n\n :userList\n\nYou can manage group members in the group detail page."
        ],
        'applied'=> [
            'title' => 'Some users applied to join your group :name',
            'desc'  => "The following users applied to join your group :groupInfo :\n\n :userList\n\nYou can manage group members in the group detail page."
        ],
        'invited' => [
            'title' => ':sender_name invites you to group :group_name',
            'desc'  => "Hi, Dear **:reciver_name**,\n\n  **:sender_name** has just invited you to join the group **[:group_name](:group_url)**. Take a look and meet other fascinating people right now!\n\nSincerely, NOJ"
        ]
    ],
    'homework' => [
        'new' => [
            'title' => 'You\'ve got one or more new assignments',
            'desc'  => "The following assignment(s) have been released by admin or manager of groups you participate, please take your time and have a look:\n\n :homeworkList\n\nYou can complete them so long as time don't pass the deadline."
        ]
    ],
];
