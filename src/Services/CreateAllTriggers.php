<?php

namespace Odinbi\ActivityLogsWithTrigger\Services;

use Illuminate\Console\Command;
use App\Services\TriggerService;


class CreateAllTriggers extends Command
{
    protected $signature = 'db:create-all-triggers';
    protected $description = 'Create database triggers for all specified tables';

    /**
     * Xử lý tạo triggers cho tất cả các tables.
     *
     * Phương thức này sẽ lặp qua danh sách các tables cần tạo trigger và gọi lệnh tạo trigger cho mỗi table.
     * Trigger sẽ được tạo mới theo cấu trúc schema mới nhất của table, nếu tôn tại.
     *
     * @return int $exitCode
     */
    public function handle()
    {
        $tables = $this->getTables();

        foreach ($tables as $table) {
            $this->info("Creating triggers for table: $table");

            $exitCode = $this->call('db:create-triggers', [
                'table' => $table,
            ]);

            if ($exitCode !== SymfonyCommand::SUCCESS) {
                $this->error("Failed to create triggers for table: $table");
                return $exitCode;
            }
        }

        $this->info('All triggers created successfully.');
        return SymfonyCommand::SUCCESS;
    }

    /**
     * Lấy ra danh sách tables cần tạo trigger.
     *
     * @return array $tables
     */
    private function getTables()
    {
        return config('activity-logs-trigger.tables', []);
    }
}
