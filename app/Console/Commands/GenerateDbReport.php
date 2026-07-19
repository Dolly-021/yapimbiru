<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GenerateDbReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:db-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a JSON report of tables, constraints, views and triggers from the current database.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Gathering DB metadata...');

        $database = DB::getDatabaseName();

        // Tables
        $tables = DB::select('SELECT TABLE_NAME, TABLE_TYPE FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?', [$database]);

        // Columns per table (optional small sample)
        $columns = [];
        foreach ($tables as $t) {
            $tableName = $t->TABLE_NAME;
            $cols = DB::select('SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$database, $tableName]);
            $columns[$tableName] = $cols;
        }

        // Constraints / foreign keys
        $constraints = DB::select('SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND REFERENCED_TABLE_NAME IS NOT NULL', [$database]);

        // Views
        $views = DB::select('SELECT TABLE_NAME AS VIEW_NAME, VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = ?', [$database]);

        // Triggers
        $triggers = DB::select('SELECT TRIGGER_NAME, EVENT_MANIPULATION, EVENT_OBJECT_TABLE, ACTION_STATEMENT FROM INFORMATION_SCHEMA.TRIGGERS WHERE TRIGGER_SCHEMA = ?', [$database]);

        $report = [
            'database' => $database,
            'tables' => array_map(function ($r) { return (array) $r; }, $tables),
            'columns' => array_map(function ($v) { return array_map(function($c){ return (array)$c; }, $v); }, $columns),
            'constraints' => array_map(function ($r) { return (array) $r; }, $constraints),
            'views' => array_map(function ($r) { return (array) $r; }, $views),
            'triggers' => array_map(function ($r) { return (array) $r; }, $triggers),
        ];

        $json = json_encode($report, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

        $path = 'db_report/db_report_' . date('Ymd_His') . '.json';
        Storage::put($path, $json);

        $this->info("Report written to storage/app/{$path}");
        $this->line('Summary:');
        $this->line('- Tables: ' . count($report['tables']));
        $this->line('- Views: ' . count($report['views']));
        $this->line('- Triggers: ' . count($report['triggers']));
        $this->line('- Constraints: ' . count($report['constraints']));

        return 0;
    }
}
