<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\PayrollComponent;
use App\Models\Position;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SetupWizardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        $completedSteps = $this->completedSteps($tenant);

        $steps = [
            ['id' => 1, 'name' => 'Company Profile', 'completed' => ! empty($completedSteps[1])],
            ['id' => 2, 'name' => 'Work Settings', 'completed' => ! empty($completedSteps[2])],
            ['id' => 3, 'name' => 'Departments & Positions', 'completed' => ! empty($completedSteps[3])],
            ['id' => 4, 'name' => 'Add Employees', 'completed' => ! empty($completedSteps[4])],
            ['id' => 5, 'name' => 'Setup Payroll', 'completed' => ! empty($completedSteps[5])],
        ];

        $currentStep = $request->get('step', 1);

        $departments = Department::all();
        $positions = Position::all();

        return Inertia::render('SetupWizard/Index', [
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'settings' => is_array($tenant->settings) ? $tenant->settings : [],
            ],
            'steps' => $steps,
            'currentStep' => (int) $currentStep,
            'departments' => $departments,
            'positions' => $positions,
        ]);
    }

    public function updateStep1(Request $request)
    {
        $validated = $request->validate([
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'npwp' => 'nullable|string|max:20',
        ]);

        $this->saveStep($request, 1, [
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'npwp' => $validated['npwp'] ?? null,
        ]);

        return redirect()->route('setup-wizard.index', ['step' => 2]);
    }

    public function updateStep2(Request $request)
    {
        $validated = $request->validate([
            'work_start' => 'required|string',
            'work_end' => 'required|string',
            'late_tolerance' => 'required|integer|min:0',
            'geofence_radius' => 'required|integer|min:50|max:500',
        ]);

        $this->saveStep($request, 2, [
            'work_hours' => [
                'start' => $validated['work_start'],
                'end' => $validated['work_end'],
            ],
            'late_tolerance_minutes' => $validated['late_tolerance'],
            'geofence_radius' => $validated['geofence_radius'],
        ]);

        return redirect()->route('setup-wizard.index', ['step' => 3]);
    }

    public function updateStep3(Request $request)
    {
        $validated = $request->validate([
            'departments' => 'required|array|min:1',
            'departments.*.name' => 'required|string|max:255',
            'positions' => 'required|array|min:1',
            'positions.*.name' => 'required|string|max:255',
            'positions.*.level' => 'required|integer|min:1',
        ]);

        foreach ($validated['departments'] as $dept) {
            Department::create([
                'name' => $dept['name'],
            ]);
        }

        foreach ($validated['positions'] as $pos) {
            Position::create([
                'name' => $pos['name'],
                'level' => $pos['level'],
            ]);
        }

        $this->saveStep($request, 3);

        return redirect()->route('setup-wizard.index', ['step' => 4]);
    }

    public function updateStep4(Request $request)
    {
        $validated = $request->validate([
            'employees' => 'required|array|min:1',
            'employees.*.name' => 'required|string|max:255',
            'employees.*.email' => 'required|email',
            'employees.*.department_id' => 'required|exists:departments,id',
            'employees.*.position_id' => 'required|exists:positions,id',
            'employees.*.join_date' => 'required|date',
        ]);

        foreach ($validated['employees'] as $emp) {
            $nik = 'EMP'.str_pad((string) (Employee::count() + 1), 5, '0', STR_PAD_LEFT);

            Employee::create([
                'nik' => $nik,
                'name' => $emp['name'],
                'email_personal' => $emp['email'],
                'department_id' => $emp['department_id'],
                'position_id' => $emp['position_id'],
                'join_date' => $emp['join_date'],
                'status' => 'permanent',
                'is_active' => true,
            ]);
        }

        $this->saveStep($request, 4);

        return redirect()->route('setup-wizard.index', ['step' => 5]);
    }

    public function updateStep5(Request $request)
    {
        $defaultComponents = [
            ['code' => 'BASIC', 'name' => 'Gaji Pokok', 'type' => 'earning', 'calculation_type' => 'fixed', 'default_value' => 0],
            ['code' => 'THR', 'name' => 'Tunjangan Hari Raya', 'type' => 'earning', 'calculation_type' => 'fixed', 'default_value' => 0],
            ['code' => 'TRANSPORT', 'name' => 'Tunjangan Transportasi', 'type' => 'earning', 'calculation_type' => 'fixed', 'default_value' => 0],
            ['code' => 'MEAL', 'name' => 'Tunjangan Makan', 'type' => 'earning', 'calculation_type' => 'fixed', 'default_value' => 0],
            ['code' => 'OVERTIME', 'name' => 'Uang Lembur', 'type' => 'earning', 'calculation_type' => 'variable', 'default_value' => 0],
            ['code' => 'BPJS_K', 'name' => 'BPJS Kesehatan', 'type' => 'deduction', 'calculation_type' => 'percentage', 'default_value' => 1],
            ['code' => 'BPJS_J', 'name' => 'BPJS Ketenagakerjaan', 'type' => 'deduction', 'calculation_type' => 'percentage', 'default_value' => 0.5],
        ];

        foreach ($defaultComponents as $component) {
            PayrollComponent::create(array_merge($component, [
                'is_active' => true,
            ]));
        }

        $this->saveStep($request, 5);

        return redirect()->route('dashboard')
            ->with('success', 'Setup selesai! Selamat menggunakan HRapp.');
    }

    /**
     * Get the completed setup steps for the tenant.
     *
     * @return array<int, bool>
     */
    private function completedSteps(Tenant $tenant): array
    {
        $settings = is_array($tenant->settings) ? $tenant->settings : [];

        return $settings['setup_completed_steps'] ?? [];
    }

    /**
     * Save step data to tenant settings and mark the step as completed.
     */
    private function saveStep(Request $request, int $step, array $additionalSettings = []): void
    {
        $tenant = $request->user()->tenant;

        $settings = is_array($tenant->settings) ? $tenant->settings : [];
        $completedSteps = $settings['setup_completed_steps'] ?? [];
        $completedSteps[$step] = true;

        $tenant->update([
            'settings' => array_merge($settings, $additionalSettings, [
                'setup_completed_steps' => $completedSteps,
            ]),
        ]);
    }
}
