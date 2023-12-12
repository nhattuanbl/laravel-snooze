<?php

namespace nhattuanbl\Snooze;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use nhattuanbl\Snooze\Console\Commands\NotifySnoozeCommand;

class NotifySnoozeProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/snooze.php', 'snooze');

        $this->app->bind('NotifySnooze', function () {
            return new \NotifySnoozeService();
        });

    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/snooze.php' => config_path('snooze.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'migration');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->commands([NotifySnoozeCommand::class]);

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('notify:snooze')->everyMinute();
        });
    }
}
