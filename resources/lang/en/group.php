<?php

return [
    'my'         => 'My Groups',
    'trending'   => 'Trending Groups',
    'members'    => '{0} :count Members|{1} :count Member|[2,*] :count Members',
    "create"     => [
        "title" => "Create a New Group",
        "description" => "Click here to create your own group!",
    ],
    'message'    => [
        'agreeJoin' => [
            'title'   => 'Some users agreed to join your group :name',
            'content' => ''
        ],
        'appliedJoin'=> [
            'title'   => 'Some users applied to join your group :name',
            'content' => ''
        ],
        'inviteJoin' => [
            'title'   => ':sender_name invites you to group :group_name',
            'content' => 'Hi, Dear **:reciver_name**,\n\n  **:sender_name** has just invited you to join the group **[:group_name](:group_url)**. Take a look and meet other fascinating people right now!\n\nSincerely, NOJ'
        ]
    ]
];
