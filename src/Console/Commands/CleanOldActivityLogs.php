<?php

namespace Odinbi\ActivityLogsWithTrigger\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanOldActivityLogs extends Command
{
    protected $signature = 'logs:clean-old';
    protected $description = 'Delete activity logs older than configured days';

    public function handle()
    {
        $daysToRetain = config('activity-logs-trigger.retain_days', 365);
        $cutoffDate = now()->subDays($daysToRetain);

        DB::table('activity_log_triggers')
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        $this->info('Old activity logs deleted successfully.');
    }
}
