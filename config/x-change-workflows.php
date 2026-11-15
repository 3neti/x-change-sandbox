<?php

return [
    'accounts' => env('APP_ENV') === 'local' ? [
        'App\\Models\\User:7' => [
            'aui.personal-accident.provisional-cover@1.0.0',
            'philhealth.bst.demo@1.0.0',
        ],
    ] : [],
];
