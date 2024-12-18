<?php

namespace Odinbi\ActivityLogsWithTrigger\Providers;

use Illuminate\Support\ServiceProvider;
use Odinbi\ActivityLogsWithTrigger\Services\CreateAllTriggers;
use Odinbi\ActivityLogsWithTrigger\Services\CreateDatabaseTriggers;
use Odinbi\ActivityLogsWithTrigger\Services\TriggerService;

class TriggerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Publish config
        $this->publishes([
            __DIR__.'/../../config/activity-logs-trigger.php' => config_path('activity-logs-trigger.php'),
        ], 'config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations'),
        ], 'migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateDatabaseTriggers::class,
                CreateAllTriggers::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/activity-logs-trigger.php', 'activity-logs-trigger'
        );

        $this->app->singleton(TriggerService::class, function ($app) {
            return new TriggerService();
        });
    }
}
