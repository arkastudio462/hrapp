<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'npwp' => 'nullable|string|max:20',
            'work_start' => 'nullable|string',
            'work_end' => 'nullable|string',
            'late_tolerance' => 'nullable|integer|min:0',
            'geofence_radius' => 'nullable|integer|min:50|max:500',
            'annual_leave' => 'nullable|integer|min:0',
            'sick_leave' => 'nullable|integer|min:0',
            'office_latitude' => 'nullable|numeric|between:-90,90',
            'office_longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $tenant = $request->user()->tenant;

        if ($validated['company_name'] ?? false) {
            $tenant->update(['name' => $validated['company_name']]);
        }

        $tenant->update([
            'settings' => array_merge($tenant->settings ?? [], [
                'address' => $validated['address'] ?? $tenant->settings['address'] ?? null,
                'phone' => $validated['phone'] ?? $tenant->settings['phone'] ?? null,
                'npwp' => $validated['npwp'] ?? $tenant->settings['npwp'] ?? null,
                'work_hours' => [
                    'start' => $validated['work_start'] ?? $tenant->settings['work_hours']['start'] ?? '08:00',
                    'end' => $validated['work_end'] ?? $tenant->settings['work_hours']['end'] ?? '17:00',
                ],
                'late_tolerance_minutes' => $validated['late_tolerance'] ?? $tenant->settings['late_tolerance_minutes'] ?? 15,
                'geofence_radius' => $validated['geofence_radius'] ?? $tenant->settings['geofence_radius'] ?? 100,
                'annual_leave' => $validated['annual_leave'] ?? $tenant->settings['annual_leave'] ?? 12,
                'sick_leave' => $validated['sick_leave'] ?? $tenant->settings['sick_leave'] ?? 0,
                'office_latitude' => $validated['office_latitude'] ?? $tenant->settings['office_latitude'] ?? -6.2088,
                'office_longitude' => $validated['office_longitude'] ?? $tenant->settings['office_longitude'] ?? 106.8456,
            ]),
        ]);

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
