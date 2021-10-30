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
            'desc'  => "Hi, Dear **:receiverName**,\n\nYour submitted solutions to the following problems have been accepted: \n\n :problemList\n\nSincerely, :siteName",
        ],
        'declined' => [
            'title' => 'Some of your solutions have been declined',
            'desc'  => "Hi, Dear **:receiverName**,\n\nYour submitted solutions to the following problems have been declined: \n\n :problemList\n\nSincerely, :siteName",
        ],
    ],
    'group' => [
        'agreed' => [
            'title' => 'Some users agreed to join your group :name',
            'desc'  => "Hi, Dear **:receiverName**,\n\nThe following users agreed to join your group :groupInfo :\n\n :userList\n\nYou can manage group members in the group detail page.\n\nSincerely, :siteName"
        ],
        'applied'=> [
            'title' => 'Some users applied to join your group :name',
            'desc'  => "Hi, Dear **:receiverName**,\n\nThe following users applied to join your group :groupInfo :\n\n :userList\n\nYou can manage group members in the group detail page.\n\nSincerely, :siteName"
        ],
        'invited' => [
            'title' => ':senderName invites you to group :groupName',
            'desc'  => "Hi, Dear **:receiverName**,\n\n**:senderName** have just invited you to join the group **:groupInfo**. Take a look and meet other fascinating people right now!\n\nSincerely, :siteName"
        ]
    ],
    'homework' => [
        'new' => [
            'title' => 'You\'ve got one or more new assignments',
            'desc'  => "Hi, Dear **:receiverName**,\n\nThe following assignment(s) have been released by admin or manager of groups you participate, please take your time and have a look:\n\n :homeworkList\n\nYou can complete them so long as time don't pass the deadline.\n\nSincerely, :siteName"
        ]
    ],
    'rank' => [
        'up' => [
            'title' => 'Global rank up since last time you login',
            'desc'  => "Hi, Dear **:receiverName**,\n\nSince last time you login, your global rank has been recalculated and you received a rank up from originally rank to now rank **:currentRank**.\n\nPlease keep ranking up! We are happy for your gain during the process.\n\nSincerely, :siteName"
        ],
        'down' => [
            'title' => 'Global rank down since last time you login',
            'desc'  => "Hi, Dear **:receiverName**,\n\nSince last time you login, your global rank has been recalculated and you received a rank down from originally rank **:originalRank** to now rank **:currentRank**.\n\n Don't be sad, we are waiting for your glorious return!\n\nSincerely, :siteName"
        ]
    ],
];
