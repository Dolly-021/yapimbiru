<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\DatabaseProcedureService;
use App\Services\DatabaseFunctionService;
use App\Services\LogActivityService;
use App\Models\Views\VRekapPresensiSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DatabaseSyncTest extends TestCase
{
    /**
     * Test database connection
     */
    public function test_database_connection(): void
    {
        $users = User::count();
        $this->assertIsInt($users);
    }

    /**
     * Test view model
     */
    public function test_view_model(): void
    {
        $rekap = VRekapPresensiSiswa::all();
        $this->assertIsObject($rekap);
    }

    /**
     * Test database procedure service
     */
    public function test_procedure_service(): void
    {
        $service = app(DatabaseProcedureService::class);
        $this->assertInstanceOf(DatabaseProcedureService::class, $service);
    }

    /**
     * Test database function service
     */
    public function test_function_service(): void
    {
        $service = app(DatabaseFunctionService::class);
        $this->assertInstanceOf(DatabaseFunctionService::class, $service);
    }

    /**
     * Test log activity service
     */
    public function test_log_activity_service(): void
    {
        $service = app(LogActivityService::class);
        $this->assertInstanceOf(LogActivityService::class, $service);
    }

    /**
     * Test helper functions
     */
    public function test_helper_functions(): void
    {
        $this->assertTrue(function_exists('db_procedure'));
        $this->assertTrue(function_exists('db_function'));
        $this->assertTrue(function_exists('log_activity'));
        $this->assertTrue(function_exists('quick_log'));
        $this->assertTrue(function_exists('format_file_size'));
    }

    /**
     * Test format file size helper
     */
    public function test_format_file_size(): void
    {
        $this->assertEquals('1 KB', format_file_size(1024));
        $this->assertEquals('1 MB', format_file_size(1024 * 1024));
        $this->assertEquals('500 B', format_file_size(500));
    }
}
