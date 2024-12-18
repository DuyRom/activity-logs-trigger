# Odinbi activity logs

Record query history of table in mysql

## User Manual

### Install package

```bash
composer require odinbi/activity-logs-with-trigger
```

### Migrate datatable
Run php artisan migrate command to create activity_log_triggers table

```bash
php artisan migrate
```

Or run with folder path option

```bash
php artisan migrate --path=database/migrations/2024_01_01_000000_create_activity_log_triggers_table.php
```

### Publish vendor

```bash
php artisan vendor:publish --tag=odb-activity-log
```
### Configuration
Go to app/config/activity-logs-trigger.php to configure the necessary variables

- **table**: `List of tables to record history`
- **middleware_groups**: `Using middleware for web or api`
- **retain_days**: `Maximum log retention time in days`

### Schedule
Configure automatic schedule to delete old logs according to retain days, EX:

```bash
$schedule->command('logs:clean-old')->dailyAt('01:00');
```

### Command
Create triggers for all tables defined in activity-logs-trigger

```bash
php artisan db:create-all-triggers
```

Add a trigger to any table, **php artisan db:create-triggers <table>** .
For example to the users table

```bash
php artisan db:create-triggers users
```