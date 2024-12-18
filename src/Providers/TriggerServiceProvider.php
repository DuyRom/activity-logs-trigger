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

        $this->registerMiddleware();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/activity-logs-trigger.php', 'activity-logs-trigger'
        );

        $this->app->singleton(TriggerService::class, fn($app) => new TriggerService());

        $this->registerPublish();

        $this->cleanOldActivityLogs();
    }

    protected function registerPublish()
    {
        $paths = [
            __DIR__.'/../../config/activity-logs-trigger.php' => config_path('activity-logs-trigger.php'),
            __DIR__.'/../../database/migrations/' => database_path('migrations'),
        ];

        $this->publishes($paths, 'odb-activity-log');
    }

    protected function registerMiddleware()
    {
        $router = $this->app['router'];
        $groups = config('activity-logs-trigger.middleware_groups', []);

        foreach ($groups as $group) {
            $router->pushMiddlewareToGroup($group, \Odinbi\ActivityLogsWithTrigger\Http\Middleware\SetCurrentUserId::class);
        }
    }

    protected function cleanOldActivityLogs()
    {
        $this->commands([
            \Odinbi\ActivityLogsWithTrigger\Console\Commands\CleanOldActivityLogs::class,
        ]);
    }
}
