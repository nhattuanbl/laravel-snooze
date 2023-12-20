<?php

return [
    'channels' => [
        'notify_snooze' => [
            'driver' => 'daily',
            'path' => storage_path('logs/notify_snooze.log'),
            'level' => 'debug',
            'days' => 30,
            'replace_placeholders' => true,
        ]
    ]
];
