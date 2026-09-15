<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TenantTestCase;
use Tests\TestCase;

class AttendanceExportTest extends TestCase
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

    public function test_admin_can_export_attendance(): void
    {
        $employee = Employee::factory()->create();
        Attendance::factory()->create([
            'employee_id' => $employee->id,
            'date' => Carbon::today(),
            'check_in_time' => Carbon::now()->setTime(8, 0),
            'check_out_time' => Carbon::now()->setTime(17, 0),
            'status' => 'present',
            'check_in_method' => 'face',
        ]);

        $response = $this->actingAs($this->adminUser)->get('/attendances/export?date_from='.Carbon::today()->toDateString().'&date_to='.Carbon::today()->toDateString());

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
    }

    public function test_export_requires_date_from(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/attendances/export?date_to='.Carbon::today()->toDateString());

        $response->assertSessionHasErrors('date_from');
    }

    public function test_export_requires_date_to(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/attendances/export?date_from='.Carbon::today()->toDateString());

        $response->assertSessionHasErrors('date_to');
    }

    public function test_export_date_to_must_be_after_date_from(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/attendances/export?date_from='.Carbon::tomorrow()->toDateString().'&date_to='.Carbon::yesterday()->toDateString());

        $response->assertSessionHasErrors('date_to');
    }

    public function test_export_contains_correct_data(): void
    {
        $employee = Employee::factory()->create([
            'nik' => 'EMP00001',
            'name' => 'John Doe',
        ]);

        Attendance::factory()->create([
            'employee_id' => $employee->id,
            'date' => Carbon::today(),
            'check_in_time' => Carbon::today()->setTime(8, 30),
            'check_out_time' => Carbon::today()->setTime(17, 30),
            'status' => 'present',
            'check_in_method' => 'qr',
        ]);

        $response = $this->actingAs($this->adminUser)->get('/attendances/export?date_from='.Carbon::today()->toDateString().'&date_to='.Carbon::today()->toDateString());

        $content = $response->streamedContent();
        $this->assertStringContainsString('NIK', $content);
        $this->assertStringContainsString('Nama', $content);
        $this->assertStringContainsString('Tanggal', $content);
        $this->assertStringContainsString('Status', $content);
    }

    public function test_employee_cannot_export_attendance(): void
    {
        $response = $this->actingAs($this->employeeUser)->get('/attendances/export?date_from='.Carbon::today()->toDateString().'&date_to='.Carbon::today()->toDateString());

        $response->assertStatus(403);
    }
}
