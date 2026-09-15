<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TenantTestCase;
use Tests\TestCase;

class LeaveManagementTest extends TestCase
{
    use RefreshDatabase, TenantTestCase;

    private User $adminUser;

    private User $hrUser;

    private User $employeeUser;

    private Employee $employee;

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

        $this->employee = Employee::factory()->create();
        $this->employeeUser->update(['employee_id' => $this->employee->id]);
    }

    protected function tearDown(): void
    {
        $this->endTenancy();
        parent::tearDown();
    }

    public function test_employee_can_create_leave_request(): void
    {
        $response = $this->actingAs($this->employeeUser)->post('/my-leave', [
            'type' => 'leave',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'reason' => 'Family vacation',
        ]);

        $response->assertRedirect(route('employee-portal.leaves'));
        $this->assertDatabaseHas('leave_requests', [
            'employee_id' => $this->employee->id,
            'type' => 'leave',
            'status' => 'pending',
        ]);
    }

    public function test_hr_can_approve_leave_request(): void
    {
        $leaveRequest = LeaveRequest::factory()->pending()->create([
            'employee_id' => $this->employee->id,
            'type' => 'leave',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(2),
        ]);

        $response = $this->actingAs($this->hrUser)->post("/leaves/{$leaveRequest->id}/approve");

        $response->assertRedirect();
        $leaveRequest->refresh();
        $this->assertEquals('approved', $leaveRequest->status);
        $this->assertEquals($this->hrUser->id, $leaveRequest->approved_by);
    }

    public function test_hr_can_reject_leave_request(): void
    {
        $leaveRequest = LeaveRequest::factory()->pending()->create([
            'employee_id' => $this->employee->id,
        ]);

        $response = $this->actingAs($this->hrUser)->post("/leaves/{$leaveRequest->id}/reject");

        $response->assertRedirect();
        $leaveRequest->refresh();
        $this->assertEquals('rejected', $leaveRequest->status);
        $this->assertEquals($this->hrUser->id, $leaveRequest->approved_by);
    }

    public function test_leave_balance_is_updated_on_approval(): void
    {
        $balance = LeaveBalance::factory()->annual()->create([
            'employee_id' => $this->employee->id,
            'year' => Carbon::now()->year,
            'total' => 12,
            'used' => 2,
            'remaining' => 10,
        ]);

        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(4);

        $leaveRequest = LeaveRequest::factory()->pending()->create([
            'employee_id' => $this->employee->id,
            'type' => 'leave',
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $this->actingAs($this->hrUser)->post("/leaves/{$leaveRequest->id}/approve");

        $balance->refresh();
        $this->assertEquals(7, $balance->used);
        $this->assertEquals(5, $balance->remaining);
    }

    public function test_create_leave_request_requires_valid_dates(): void
    {
        $response = $this->actingAs($this->employeeUser)->post('/my-leave', [
            'type' => 'leave',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::yesterday()->toDateString(),
            'reason' => 'Invalid dates',
        ]);

        $response->assertSessionHasErrors('end_date');
    }

    public function test_create_leave_request_requires_reason(): void
    {
        $response = $this->actingAs($this->employeeUser)->post('/my-leave', [
            'type' => 'leave',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->toDateString(),
            'reason' => '',
        ]);

        $response->assertSessionHasErrors('reason');
    }

    public function test_admin_can_view_leave_requests(): void
    {
        LeaveRequest::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)->get('/leaves');

        $response->assertStatus(200);
    }
}
