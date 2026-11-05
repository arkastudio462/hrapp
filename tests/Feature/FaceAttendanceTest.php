<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TenantTestCase;
use Tests\TestCase;

class FaceAttendanceTest extends TestCase
{
    use RefreshDatabase, TenantTestCase;

    private User $employeeUser;

    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initializeTenancy();

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

    public function test_employee_can_view_face_attendance_page_with_registered_face(): void
    {
        $this->employee->update(['face_data' => ['faces/registered.jpg']]);

        $response = $this->actingAs($this->employeeUser)->get('/face-attendance');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('FaceAttendance/Index')
            ->where('employee.id', $this->employee->id)
            ->where('hasFaceData', true));
    }

    public function test_face_attendance_page_loads_employee_without_face_data(): void
    {
        $response = $this->actingAs($this->employeeUser)->get('/face-attendance');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('FaceAttendance/Index')
            ->where('employee.id', $this->employee->id)
            ->where('hasFaceData', false));
    }

    public function test_face_attendance_page_rejects_user_without_employee_record(): void
    {
        $userWithoutEmployee = User::factory()->employee()->create([
            'tenant_id' => 'test-tenant',
        ]);

        $response = $this->actingAs($userWithoutEmployee)->get('/face-attendance');

        $response->assertRedirect();
        $response->assertSessionHasErrors('employee');
    }

    public function test_employee_can_register_face_photos(): void
    {
        Storage::fake('tenant');

        $response = $this->actingAs($this->employeeUser)->post('/face-attendance/register', [
            'face_data' => [
                'data:image/jpeg;base64,'.base64_encode('photo-one'),
                'data:image/jpeg;base64,'.base64_encode('photo-two'),
                'data:image/jpeg;base64,'.base64_encode('photo-three'),
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->employee->refresh();
        $this->assertCount(3, $this->employee->face_data);

        foreach ($this->employee->face_data as $file) {
            $this->assertStringStartsWith('faces/', $file);
            Storage::disk('tenant')->assertExists($file);
        }
    }

    public function test_employee_can_check_in_with_face_within_geofence(): void
    {
        Storage::fake('tenant');

        $tenant = Tenant::find('test-tenant');
        $tenant->settings = [
            'attendance_method' => 'face_location',
            'office_latitude' => -6.2,
            'office_longitude' => 106.8,
            'geofence_radius' => 100,
        ];
        $tenant->save();

        $this->employee->update(['face_data' => ['faces/registered.jpg']]);

        $response = $this->actingAs($this->employeeUser)->post('/face-attendance/verify', [
            'photo' => 'data:image/jpeg;base64,'.base64_encode('checkin-photo'),
            'latitude' => -6.2001,
            'longitude' => 106.8001,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('attendances', 1);

        $attendance = $this->employee->attendances()->first();
        $this->assertNotNull($attendance);
        $this->assertSame(now()->toDateString(), $attendance->date->toDateString());
        $this->assertSame('face', $attendance->check_in_method);
        $this->assertStringStartsWith('attendance/', $attendance->check_in_photo);
        Storage::disk('tenant')->assertExists($attendance->check_in_photo);
    }

    public function test_check_in_is_rejected_outside_geofence(): void
    {
        Storage::fake('tenant');

        $tenant = Tenant::find('test-tenant');
        $tenant->settings = [
            'attendance_method' => 'face_location',
            'office_latitude' => -6.2,
            'office_longitude' => 106.8,
            'geofence_radius' => 100,
        ];
        $tenant->save();

        $this->employee->update(['face_data' => ['faces/registered.jpg']]);

        $response = $this->actingAs($this->employeeUser)->post('/face-attendance/verify', [
            'photo' => 'data:image/jpeg;base64,'.base64_encode('checkin-photo'),
            'latitude' => -6.4,
            'longitude' => 107.0,
        ]);

        $response->assertSessionHasErrors('geofence');
        $this->assertDatabaseMissing('attendances', [
            'employee_id' => $this->employee->id,
        ]);
    }
}
