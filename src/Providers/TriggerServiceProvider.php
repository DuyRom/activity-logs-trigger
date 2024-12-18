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

        $this->app->singleton(TriggerService::class, fn($app) => new TriggerService());

        $this->registerPublish();
    }

    protected function registerPublish()
    {
        $paths = [
            __DIR__.'/../../config/activity-logs-trigger.php' => config_path('activity-logs-trigger.php'),
            __DIR__.'/../../database/migrations/' => database_path('migrations'),
        ];

        $this->publishes($paths, 'odb-activity-log');
    }
}
