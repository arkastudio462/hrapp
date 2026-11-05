<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'departments',
            'positions',
            'employees',
            'attendances',
            'qr_codes',
            'leave_requests',
            'leave_balances',
            'payroll_periods',
            'payrolls',
            'payroll_items',
            'payroll_components',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('tenant_id')->nullable()->index();
                    $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'departments',
            'positions',
            'employees',
            'attendances',
            'qr_codes',
            'leave_requests',
            'leave_balances',
            'payroll_periods',
            'payrolls',
            'payroll_items',
            'payroll_components',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['tenant_id']);
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
};
