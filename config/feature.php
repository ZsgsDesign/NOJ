<?php

return [
    'submission' => [
        'sharing' => env('FEATURE_ENABLE_SUBMISSION_SHARING', true),
    ],
    'tools' => [
        'pastebin' => env('FEATURE_ENABLE_TOOLS_PASTEBIN', true),
        'imagehosting' => env('FEATURE_ENABLE_TOOLS_IMAGEHOSTING', true),
    ],
    'rank' => env('FEATURE_ENABLE_RANK', true),
    'group' => env('FEATURE_ENABLE_GROUP', true),
    'dojo' => env('FEATURE_ENABLE_DOJO', true),
    'api' => env('FEATURE_ENABLE_API', true),
    'search' => env('FEATURE_ENABLE_SEARCH', true),
    'problem' => [
        'discussion' => [
            'solution' => env('FEATURE_ENABLE_PROBLEM_DISCUSSION_SOLUTION', true),
            'article' => env('FEATURE_ENABLE_PROBLEM_DISCUSSION_ARTICLE', true),
        ],
    ],
    'contest' => [
        'clarification' => env('FEATURE_ENABLE_CONTEST_CLARIFICATION', true),
        'print' => env('FEATURE_ENABLE_CONTEST_PRINT', true),
    ],
];
