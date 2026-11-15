<?php

return [
    'operator_user_ids' => array_values(array_filter(
        array_map('intval', explode(',', (string) env('XCHANGE_SANDBOX_STORED_VALUE_COLLECTOR_USER_IDS', ''))),
        static fn (int $id): bool => $id > 0,
    )),
];
