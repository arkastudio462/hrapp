<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TenantTestCase;
use Tests\TestCase;

class DepartmentManagementTest extends TestCase
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

    public function test_admin_can_view_department_list(): void
    {
        Department::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)->get('/departments');

        $response->assertStatus(200);
    }

    public function test_hr_can_view_department_list(): void
    {
        Department::factory()->count(2)->create();

        $response = $this->actingAs($this->hrUser)->get('/departments');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_department(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/departments', [
            'name' => 'Engineering',
            'budget' => 5000000,
        ]);

        $response->assertRedirect('/departments');
        $this->assertDatabaseHas('departments', [
            'name' => 'Engineering',
        ]);
    }

    public function test_admin_can_update_department(): void
    {
        $department = Department::factory()->create();

        $response = $this->actingAs($this->adminUser)->put("/departments/{$department->id}", [
            'name' => 'Updated Department',
            'budget' => 10000000,
        ]);

        $response->assertRedirect('/departments');
        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'name' => 'Updated Department',
        ]);
    }

    public function test_admin_can_delete_department(): void
    {
        $department = Department::factory()->create();

        $response = $this->actingAs($this->adminUser)->delete("/departments/{$department->id}");

        $response->assertRedirect('/departments');
        $this->assertDatabaseMissing('departments', [
            'id' => $department->id,
        ]);
    }

    public function test_create_department_requires_name(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/departments', [
            'name' => '',
            'budget' => 5000000,
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_create_department_requires_positive_budget(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/departments', [
            'name' => 'Engineering',
            'budget' => -1000,
        ]);

        $response->assertSessionHasErrors('budget');
    }

    public function test_employee_cannot_access_department_management(): void
    {
        $response = $this->actingAs($this->employeeUser)->get('/departments');

        $response->assertStatus(403);
    }
}
