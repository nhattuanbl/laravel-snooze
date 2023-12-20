<?php

namespace Nhattuanbl\Snooze;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Nhattuanbl\Snooze\Console\Commands\NotifySnoozeCommand;
use Nhattuanbl\Snooze\Facades\Snooze;
use Nhattuanbl\Snooze\Services\NotifySnoozeService;

class NotifySnoozeProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/snooze.php', 'snooze');
        $this->mergeConfigFrom(__DIR__.'/../config/logging.php', 'logging.channels');

        $this->app->bind('NotifySnooze', function () {
            return new NotifySnoozeService();
        });

        $this->app->register(EventServiceProvider::class);
        $loader = AliasLoader::getInstance();
        $loader->alias('Snooze', Snooze::class);
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
