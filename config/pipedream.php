<?php

return [
    'base_url' => env('PIPEDREAM_BASE_URL', 'https://api.pipedream.com'),

    'project_id' => env('PIPEDREAM_PROJECT_ID'),

    'environment' => env('PIPEDREAM_ENVIRONMENT', 'production'),

    'client_id' => env('PIPEDREAM_CLIENT_ID'),

    'client_secret' => env('PIPEDREAM_CLIENT_SECRET'),

    'oauth_scope' => env('PIPEDREAM_OAUTH_SCOPE', 'connect:tokens:create'),

    'connect_token_scope' => env(
        'PIPEDREAM_CONNECT_TOKEN_SCOPE',
        'connect:accounts:read connect:accounts:write'
    ),

    'external_user_namespace' => env('PIPEDREAM_EXTERNAL_USER_NAMESPACE', env('APP_URL')),

    'allowed_apps' => ['gmail', 'slack', 'google_sheets'],

    'token_expires_in' => (int) env('PIPEDREAM_CONNECT_TOKEN_EXPIRES_IN', 14400),

    'connect_timeout' => (int) env('PIPEDREAM_CONNECT_TIMEOUT', 5),

    'timeout' => (int) env('PIPEDREAM_TIMEOUT', 15),

    'allowed_origins' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('PIPEDREAM_ALLOWED_ORIGINS', ''))
    ))),
];
