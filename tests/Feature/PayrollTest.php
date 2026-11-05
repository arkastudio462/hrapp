<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollComponent;
use App\Models\PayrollPeriod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TenantTestCase;
use Tests\TestCase;

class PayrollTest extends TestCase
{
    use RefreshDatabase, TenantTestCase;

    private User $adminUser;

    private User $employeeUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initializeTenancy();

        $this->adminUser = User::factory()->admin()->create([
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

    public function test_admin_can_view_payroll_periods(): void
    {
        PayrollPeriod::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)->get('/payroll');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_payroll_period(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/payroll', [
            'month' => 9,
            'year' => 2026,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payroll_periods', [
            'month' => 9,
            'year' => 2026,
            'status' => 'draft',
        ]);
    }

    public function test_admin_cannot_create_duplicate_payroll_period(): void
    {
        PayrollPeriod::factory()->create([
            'month' => 9,
            'year' => 2026,
            'tenant_id' => 'test-tenant',
        ]);

        $response = $this->actingAs($this->adminUser)->post('/payroll', [
            'month' => 9,
            'year' => 2026,
        ]);

        $response->assertSessionHasErrors('month');
    }

    public function test_payroll_period_can_be_created_when_other_tenant_uses_same_month(): void
    {
        // All tenants share one database, so the unique index must be scoped per tenant.
        DB::table('payroll_periods')->insert([
            'month' => 9,
            'year' => 2026,
            'status' => 'draft',
            'tenant_id' => 'other-tenant',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)->post('/payroll', [
            'month' => 9,
            'year' => 2026,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payroll_periods', [
            'month' => 9,
            'year' => 2026,
            'tenant_id' => 'test-tenant',
        ]);
    }

    public function test_admin_can_process_payroll(): void
    {
        $period = PayrollPeriod::factory()->draft()->create([
            'month' => 9,
            'year' => 2026,
        ]);

        $employee = Employee::factory()->create([
            'is_active' => true,
        ]);

        PayrollComponent::factory()->create([
            'code' => 'GAJI',
            'type' => 'earning',
            'default_value' => 5000000,
        ]);

        $response = $this->actingAs($this->adminUser)->post("/payroll/{$period->id}/process");

        $response->assertRedirect();
        $period->refresh();
        $this->assertEquals('completed', $period->status);

        $this->assertDatabaseHas('payrolls', [
            'employee_id' => $employee->id,
            'payroll_period_id' => $period->id,
        ]);
    }

    public function test_admin_cannot_process_completed_period(): void
    {
        $period = PayrollPeriod::factory()->completed()->create();

        $response = $this->actingAs($this->adminUser)->post("/payroll/{$period->id}/process");

        $response->assertSessionHasErrors('period');
    }

    public function test_payroll_calculates_correctly(): void
    {
        $period = PayrollPeriod::factory()->draft()->create([
            'month' => 9,
            'year' => 2026,
        ]);

        $employee = Employee::factory()->create([
            'is_active' => true,
        ]);

        PayrollComponent::factory()->create([
            'code' => 'GAJI',
            'type' => 'earning',
            'default_value' => 5000000,
        ]);

        PayrollComponent::factory()->create([
            'code' => 'TUNJANGAN',
            'type' => 'earning',
            'default_value' => 1000000,
        ]);

        PayrollComponent::factory()->create([
            'code' => 'BPJS',
            'type' => 'deduction',
            'default_value' => 200000,
        ]);

        $this->actingAs($this->adminUser)->post("/payroll/{$period->id}/process");

        $payroll = Payroll::where('employee_id', $employee->id)
            ->where('payroll_period_id', $period->id)
            ->first();

        $this->assertNotNull($payroll);
        $this->assertEquals(6000000, $payroll->total_earnings);
        $this->assertEquals(200000, $payroll->total_deductions);
        $this->assertEquals(5800000, $payroll->net_salary);
    }

    public function test_payroll_period_requires_valid_month(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/payroll', [
            'month' => 13,
            'year' => 2026,
        ]);

        $response->assertSessionHasErrors('month');
    }

    public function test_employee_cannot_access_payroll(): void
    {
        $response = $this->actingAs($this->employeeUser)->get('/payroll');

        $response->assertStatus(403);
    }
}
