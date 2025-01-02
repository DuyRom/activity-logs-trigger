<?php

namespace Odinbi\ActivityLogsWithTrigger\Services;

use Illuminate\Support\Facades\DB;

class TriggerService
{
    public function createTriggers($tableName, $excludedColumns = [])
    {
        $this->dropTriggers($tableName);

        $columns = $this->getTableColumns($tableName, $excludedColumns);

        $newValuesJson = implode(', ', array_map(function ($column) {
            return "\"$column\", NEW.$column";
        }, $columns));

        $oldValuesJson = implode(', ', array_map(function ($column) {
            return "\"$column\", OLD.$column";
        }, $columns));

        // Get primary key configuration
        $primaryKeysConfig = config("activity-logs-trigger.primary_keys.$tableName", ['id']);
        $primaryKeyNew = implode(', ', array_map(function ($key) {
            return "NEW.$key";
        }, $primaryKeysConfig));
        $primaryKeyOld = implode(', ', array_map(function ($key) {
            return "OLD.$key";
        }, $primaryKeysConfig));

        // Create triggers after insert
        DB::unprepared("
            CREATE TRIGGER after_{$tableName}_insert 
            AFTER INSERT ON $tableName 
            FOR EACH ROW 
            BEGIN
                DECLARE v_user_id INT;
                DECLARE new_values_json TEXT;

                SET v_user_id = (SELECT @current_user_id);
                SET new_values_json = JSON_OBJECT($newValuesJson);

                INSERT INTO activity_log_triggers (
                    user_id, 
                    action, 
                    table_name, 
                    primary_key,
                    old_values, 
                    new_values, 
                    created_at, 
                    updated_at
                ) 
                VALUES (
                    v_user_id, 
                    'insert', 
                    '$tableName', 
                    CONCAT_WS(',', $primaryKeyNew),
                    NULL, 
                    new_values_json, 
                    NOW(), 
                    NOW()
                );
            END;
        ");

        // Create triggers after update
        DB::unprepared("
            CREATE TRIGGER after_{$tableName}_update 
            AFTER UPDATE ON $tableName 
            FOR EACH ROW 
            BEGIN
                DECLARE v_user_id INT;
                DECLARE old_values_json TEXT;
                DECLARE new_values_json TEXT;

                SET v_user_id = (SELECT @current_user_id);
                SET old_values_json = '{}';
                SET new_values_json = '{}';

                IF CHAR_LENGTH(JSON_OBJECT($oldValuesJson)) > 2 THEN
                    SET old_values_json = JSON_OBJECT($oldValuesJson);
                    SET new_values_json = JSON_OBJECT($newValuesJson);

                    INSERT INTO activity_log_triggers (
                        user_id, 
                        action, 
                        table_name, 
                        primary_key,
                        old_values, 
                        new_values, 
                        created_at, 
                        updated_at
                    ) 
                    VALUES (
                        v_user_id, 
                        'update', 
                        '$tableName', 
                        CONCAT_WS(',', $primaryKeyOld),
                        old_values_json, 
                        new_values_json, 
                        NOW(), 
                        NOW()
                    );
                END IF;
            END;
        ");

        // Create triggers after delete
        DB::unprepared("
            CREATE TRIGGER after_{$tableName}_delete 
            AFTER DELETE ON $tableName 
            FOR EACH ROW 
            BEGIN
                DECLARE v_user_id INT;
                DECLARE old_values_json TEXT;

                SET v_user_id = (SELECT @current_user_id);
                SET old_values_json = JSON_OBJECT($oldValuesJson);

                INSERT INTO activity_log_triggers (
                    user_id, 
                    action, 
                    table_name, 
                    primary_key,
                    old_values, 
                    new_values, 
                    created_at, 
                    updated_at
                ) 
                VALUES (
                    v_user_id, 
                    'delete', 
                    '$tableName', 
                    CONCAT_WS(',', $primaryKeyOld),
                    old_values_json, 
                    NULL, 
                    NOW(), 
                    NOW()
                );
            END;
        ");
    }

    private function dropTriggers($tableName)
    {
        DB::unprepared("DROP TRIGGER IF EXISTS after_{$tableName}_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS after_{$tableName}_update");
        DB::unprepared("DROP TRIGGER IF EXISTS after_{$tableName}_delete");
    }

    private function getTableColumns($tableName, $excludedColumns = [])
    {
        $columns = DB::getSchemaBuilder()->getColumnListing($tableName);
        return array_diff($columns, $excludedColumns);
    }
}
