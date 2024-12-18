<?php

namespace Odinbi\ActivityLogsWithTrigger\Services;

use Illuminate\Console\Command;

class CreateDatabaseTriggers extends Command
{
    protected $signature = 'db:create-triggers {table}';
    protected $description = 'Create database triggers for a specified table';

    protected $triggerService;

    public function __construct(TriggerService $triggerService)
    {
        parent::__construct();
        $this->triggerService = $triggerService;
    }

    public function handle()
    {
        $table = $this->argument('table');
        $excludedColumns = ['id'];

        $this->triggerService->createTriggers($table, $excludedColumns);

        $this->info("Database triggers created successfully for table: $table.");
    }
}
