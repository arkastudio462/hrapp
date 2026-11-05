<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TenantTestCase;
use Tests\TestCase;

class EmployeeManagementTest extends TestCase
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

    public function test_admin_can_view_employee_list(): void
    {
        Employee::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)->get('/employees');

        $response->assertStatus(200);
    }

    public function test_hr_can_view_employee_list(): void
    {
        Employee::factory()->count(2)->create();

        $response = $this->actingAs($this->hrUser)->get('/employees');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_employee(): void
    {
        $department = Department::factory()->create();
        $position = Position::factory()->create();

        $response = $this->actingAs($this->adminUser)->post('/employees', [
            'name' => 'John Doe',
            'email_personal' => 'john@example.com',
            'phone' => '08123456789',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'address' => 'Jl. Test No. 1',
            'department_id' => $department->id,
            'position_id' => $position->id,
            'join_date' => now()->toDateString(),
            'status' => 'permanent',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/employees');
        $this->assertDatabaseHas('employees', [
            'name' => 'John Doe',
            'email_personal' => 'john@example.com',
        ]);
    }

    public function test_admin_can_update_employee(): void
    {
        $employee = Employee::factory()->create();
        $department = Department::factory()->create();

        $response = $this->actingAs($this->adminUser)->put("/employees/{$employee->id}", [
            'name' => 'Jane Doe',
            'email_personal' => 'jane@example.com',
            'department_id' => $department->id,
            'position_id' => $employee->position_id,
            'join_date' => $employee->join_date->toDateString(),
            'status' => 'permanent',
            'is_active' => true,
        ]);

        $response->assertRedirect('/employees');
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'name' => 'Jane Doe',
        ]);
    }

    public function test_admin_can_delete_employee(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->actingAs($this->adminUser)->delete("/employees/{$employee->id}");

        $response->assertRedirect('/employees');
        $this->assertDatabaseMissing('employees', [
            'id' => $employee->id,
        ]);
    }

    public function test_create_employee_requires_name(): void
    {
        $department = Department::factory()->create();
        $position = Position::factory()->create();

        $response = $this->actingAs($this->adminUser)->post('/employees', [
            'name' => '',
            'department_id' => $department->id,
            'position_id' => $position->id,
            'join_date' => now()->toDateString(),
            'status' => 'permanent',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_create_employee_requires_valid_department(): void
    {
        $position = Position::factory()->create();

        $response = $this->actingAs($this->adminUser)->post('/employees', [
            'name' => 'John Doe',
            'department_id' => 9999,
            'position_id' => $position->id,
            'join_date' => now()->toDateString(),
            'status' => 'permanent',
        ]);

        $response->assertSessionHasErrors('department_id');
    }

    public function test_employee_cannot_access_employee_management(): void
    {
        Employee::factory()->create();

        $response = $this->actingAs($this->employeeUser)->get('/employees');

        $response->assertStatus(403);
    }
}
