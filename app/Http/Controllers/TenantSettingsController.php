<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TenantSettingsRequest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TenantSettingsController extends Controller
{
    public function edit(Request $request)
    {
        $tenant = $request->user()->tenant;

        return Inertia::render('Settings/Index', [
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'settings' => $tenant->settings,
            ],
        ]);
    }

    public function update(TenantSettingsRequest $request)
    {
        $validated = $request->validated();

        $tenant = $request->user()->tenant;

        if ($validated['company_name'] ?? false) {
            $tenant->update(['name' => $validated['company_name']]);
        }

        $currentSettings = is_array($tenant->settings) ? $tenant->settings : [];

        $tenant->update([
            'settings' => array_merge($currentSettings, [
                'address' => $validated['address'] ?? $currentSettings['address'] ?? null,
                'phone' => $validated['phone'] ?? $currentSettings['phone'] ?? null,
                'npwp' => $validated['npwp'] ?? $currentSettings['npwp'] ?? null,
                'work_hours' => [
                    'start' => $validated['work_start'] ?? $currentSettings['work_hours']['start'] ?? '08:00',
                    'end' => $validated['work_end'] ?? $currentSettings['work_hours']['end'] ?? '17:00',
                ],
                'late_tolerance_minutes' => $validated['late_tolerance'] ?? $currentSettings['late_tolerance_minutes'] ?? 15,
                'geofence_radius' => $validated['geofence_radius'] ?? $currentSettings['geofence_radius'] ?? 100,
                'annual_leave' => $validated['annual_leave'] ?? $currentSettings['annual_leave'] ?? 12,
                'sick_leave' => $validated['sick_leave'] ?? $currentSettings['sick_leave'] ?? 0,
                'office_latitude' => $validated['office_latitude'] ?? $currentSettings['office_latitude'] ?? null,
                'office_longitude' => $validated['office_longitude'] ?? $currentSettings['office_longitude'] ?? null,
                'attendance_method' => $validated['attendance_method'] ?? $currentSettings['attendance_method'] ?? 'face_location',
            ]),
        ]);

        ActivityLog::log('settings.updated', null, null, 'Tenant settings updated');

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
