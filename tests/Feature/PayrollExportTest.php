<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TenantTestCase;
use Tests\TestCase;

class PayrollExportTest extends TestCase
{
    use RefreshDatabase, TenantTestCase;

    private User $adminUser;

    private User $hrUser;

    private User $employeeUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initializeTenancy();

        $this->adminUser = User::factory()->admin()->create([
            'tenant_id' => 'test-tenant',
        ]);
        $this->hrUser = User::factory()->hr()->create([
            'tenant_id' => 'test-tenant',
        ]);
        $this->employeeUser = User::factory()->employee()->create([
            'tenant_id' => 'test-tenant',
        ]);
    }

    protected function tearDown(): void
    {
        $this->endTenancy();
        parent::tearDown();
    }

    public function test_admin_can_export_payroll(): void
    {
        $period = PayrollPeriod::factory()->completed()->create([
            'month' => 9,
            'year' => 2026,
        ]);

        $employee = Employee::factory()->create();
        Payroll::factory()->create([
            'employee_id' => $employee->id,
            'payroll_period_id' => $period->id,
            'basic_salary' => 5000000,
            'total_earnings' => 1000000,
            'total_deductions' => 500000,
            'net_salary' => 5500000,
        ]);

        $response = $this->actingAs($this->adminUser)->get("/payroll/{$period->id}/export");

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
    }

    public function test_export_contains_correct_headers(): void
    {
        $period = PayrollPeriod::factory()->completed()->create([
            'month' => 9,
            'year' => 2026,
        ]);

        $response = $this->actingAs($this->adminUser)->get("/payroll/{$period->id}/export");

        $content = $response->streamedContent();
        $this->assertStringContainsString('NIK', $content);
        $this->assertStringContainsString('Nama', $content);
        $this->assertStringContainsString('Jabatan', $content);
        $this->assertStringContainsString('Gaji Pokok', $content);
        $this->assertStringContainsString('Total Tunjangan', $content);
        $this->assertStringContainsString('Total Potongan', $content);
        $this->assertStringContainsString('Gaji Bersih', $content);
    }

    public function test_export_contains_payroll_data(): void
    {
        $period = PayrollPeriod::factory()->completed()->create([
            'month' => 9,
            'year' => 2026,
        ]);

        $employee = Employee::factory()->create([
            'nik' => 'EMP00001',
            'name' => 'John Doe',
        ]);

        Payroll::factory()->create([
            'employee_id' => $employee->id,
            'payroll_period_id' => $period->id,
            'basic_salary' => 5000000,
            'total_earnings' => 1000000,
            'total_deductions' => 500000,
            'net_salary' => 5500000,
        ]);

        $response = $this->actingAs($this->adminUser)->get("/payroll/{$period->id}/export");

        $content = $response->streamedContent();
        $this->assertStringContainsString('EMP00001', $content);
        $this->assertStringContainsString('John Doe', $content);
        $this->assertStringContainsString('5000000.00', $content);
        $this->assertStringContainsString('1000000.00', $content);
        $this->assertStringContainsString('500000.00', $content);
        $this->assertStringContainsString('5500000.00', $content);
    }

    public function test_employee_cannot_export_payroll(): void
    {
        $period = PayrollPeriod::factory()->completed()->create();

        $response = $this->actingAs($this->employeeUser)->get("/payroll/{$period->id}/export");

        $response->assertStatus(403);
    }

    public function test_hr_can_export_payroll(): void
    {
        $period = PayrollPeriod::factory()->completed()->create();

        $response = $this->actingAs($this->hrUser)->get("/payroll/{$period->id}/export");

        $response->assertStatus(200);
    }
}
