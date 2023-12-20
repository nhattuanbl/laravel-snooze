<?php

return [
    'connection' => env('NOTIFY_SNOOZE_CONNECTION', env('DB_CONNECTION')),
    'no_sql' => env('NOTIFY_SNOOZE_NO_SQL', false),
    'user_model' => env('NOTIFY_SNOOZE_USER_MODEL', \App\Models\User::class),
];
