<?php

namespace nhattuanbl\Snooze\Notifications;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\Events\NotificationSent;
use nhattuanbl\Snooze\Listeners\NotifySnoozeListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NotificationSent::class => [
            NotifySnoozeListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

}
