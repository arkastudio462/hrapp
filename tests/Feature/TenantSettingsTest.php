<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TenantTestCase;
use Tests\TestCase;

class TenantSettingsTest extends TestCase
{
    use RefreshDatabase, TenantTestCase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initializeTenancy();

        $this->adminUser = User::factory()->admin()->create([
            'tenant_id' => 'test-tenant',
        ]);
    }

    protected function tearDown(): void
    {
        $this->endTenancy();

        parent::tearDown();
    }

    public function test_admin_can_view_settings_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/settings');

        $response->assertStatus(200);
    }

    public function test_admin_can_update_settings(): void
    {
        $response = $this->actingAs($this->adminUser)->put('/settings', [
            'company_name' => 'PT Uji Coba',
            'address' => 'Jl. Uji Coba No. 1',
            'phone' => '081234567890',
            'work_start' => '08:30',
            'work_end' => '17:30',
            'late_tolerance' => 20,
            'geofence_radius' => 150,
            'annual_leave' => 14,
            'sick_leave' => 10,
            'attendance_method' => 'qr_only',
        ]);

        $response->assertRedirect();

        $tenant = Tenant::find('test-tenant');

        $this->assertSame('PT Uji Coba', $tenant->name);
        $this->assertSame('Jl. Uji Coba No. 1', $tenant->settings['address']);
        $this->assertSame(20, $tenant->settings['late_tolerance_minutes']);
        $this->assertSame('qr_only', $tenant->settings['attendance_method']);
        $this->assertSame(['start' => '08:30', 'end' => '17:30'], $tenant->settings['work_hours']);
    }

    public function test_settings_stay_an_array_across_repeated_saves(): void
    {
        $tenant = Tenant::find('test-tenant');

        for ($round = 1; $round <= 5; $round++) {
            $tenant->update([
                'settings' => array_merge($tenant->settings ?? [], ['round' => $round]),
                'limits' => ['max_employees' => 10, 'max_storage_gb' => 1],
            ]);

            $tenant = Tenant::find('test-tenant');
        }

        $this->assertIsArray($tenant->settings);
        $this->assertIsArray($tenant->limits);
        $this->assertSame(5, $tenant->settings['round']);
        $this->assertSame(10, $tenant->limits['max_employees']);

        // Each save must not add another JSON encoding layer to the data column.
        $dataLength = strlen((string) Tenant::find('test-tenant')->getRawOriginal('data'));

        $this->assertLessThan(500, $dataLength, 'tenants.data grew unexpectedly - values are being re-encoded.');
    }

    public function test_hr_cannot_update_settings(): void
    {
        $hrUser = User::factory()->hr()->create([
            'tenant_id' => 'test-tenant',
        ]);

        $response = $this->actingAs($hrUser)->put('/settings', [
            'address' => 'Should not be saved',
        ]);

        $response->assertForbidden();

        $this->assertNull(Tenant::find('test-tenant')->settings['address'] ?? null);
    }
}
