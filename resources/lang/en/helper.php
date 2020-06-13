<?php

return [
    'time' => [
        "after" => "ago",
        "before" => "from now",
        "singular" => [
            "second" => "second",
            "minute" => "minute",
            "hour" => "hour",
            "day" => "day",
            "week" => "week",
            "month" => "month",
            "year" => "year",
            "decade" => "decade"
        ],
        "plural" => [
            "second" => "seconds",
            "minute" => "minutes",
            "hour" => "hours",
            "day" => "days",
            "week" => "weeks",
            "month" => "months",
            "year" => "years",
            "decade" => "decades"
        ],
        "formatter" => ":time :unit :tense",
        "malformatter" => "Bad Date",
    ],
];
