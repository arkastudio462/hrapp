<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Facades\Tenancy;

trait TenantTestCase
{
    protected function initializeTenancy(): void
    {
        $tenant = Tenant::create([
            'id' => 'test-tenant',
            'name' => 'Test Tenant',
        ]);

        // Mirror TenantScopeMiddleware: models using BelongsToTenant only get
        // their tenant_id filled (and their global scope applied) while tenancy
        // is initialized, so factory data matches what production creates.
        tenancy()->initialize($tenant);

        $this->createTenantTables();
    }

    protected function endTenancy(): void
    {
        try {
            Tenancy::end();
        } catch (\Exception $e) {
            // Tenancy not initialized, ignore
        }
    }

    protected function createTenantTables(): void
    {
        Schema::create('employees', function ($table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('name');
            $table->string('email_personal')->nullable();
            $table->string('phone')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            $table->json('face_data')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->date('join_date');
            $table->string('status')->default('probation');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('departments', function ($table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('head_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('positions', function ($table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->integer('level')->default(1);
            $table->string('salary_grade')->nullable();
            $table->timestamps();
        });

        Schema::create('attendances', function ($table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('check_out_time')->nullable();
            $table->string('check_in_photo')->nullable();
            $table->json('check_in_location')->nullable();
            $table->string('check_in_method')->nullable();
            $table->unsignedBigInteger('qr_code_id')->nullable();
            $table->string('status')->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'date']);
        });

        Schema::create('qr_codes', function ($table) {
            $table->id();
            $table->string('code')->unique();
            $table->date('date');
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('leave_requests', function ($table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('type');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->string('attachment')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('leave_balances', function ($table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->integer('year');
            $table->string('type');
            $table->integer('total')->default(0);
            $table->integer('used')->default(0);
            $table->integer('remaining')->default(0);
            $table->timestamps();
            $table->unique(['employee_id', 'year', 'type']);
        });

        Schema::create('payroll_periods', function ($table) {
            $table->id();
            $table->integer('month');
            $table->integer('year');
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('tenant_id')->nullable();
            $table->timestamps();
            $table->unique(['month', 'year', 'tenant_id']);
        });

        Schema::create('payrolls', function ($table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('payroll_period_id');
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('total_earnings', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->unique(['employee_id', 'payroll_period_id']);
        });

        Schema::create('payroll_items', function ($table) {
            $table->id();
            $table->unsignedBigInteger('payroll_id');
            $table->string('component_code');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('type');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('payroll_components', function ($table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type');
            $table->string('calculation_type')->default('fixed');
            $table->decimal('default_value', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $this->addTenantIdColumns();
    }

    /**
     * Add the tenant_id columns created by the add_tenant_id_to_tenant_tables
     * migration, so tenant-scoped queries behave like they do in production.
     */
    private function addTenantIdColumns(): void
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
                });
            }
        }
    }
}
