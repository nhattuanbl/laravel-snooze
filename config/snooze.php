<?php

return [
    'connection' => env('NOTIFY_SNOOZE_CONNECTION', env('DB_CONNECTION', 'mysql')),
    'user_model' => env('NOTIFY_SNOOZE_USER_MODEL', \App\Models\User::class),
];
