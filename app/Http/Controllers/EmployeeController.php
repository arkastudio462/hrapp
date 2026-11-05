<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeStoreRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['department', 'position']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('email_personal', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $employees = $query->orderBy('name')->paginate(15)->withQueryString();

        return Inertia::render('Employees/Index', [
            'employees' => $employees,
            'departments' => Department::orderBy('name')->get(),
            'positions' => Position::orderBy('name')->get(),
            'filters' => $request->only(['search', 'department_id', 'status', 'is_active']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Employees/Create', [
            'departments' => Department::orderBy('name')->get(),
            'positions' => Position::orderBy('name')->get(),
        ]);
    }

    public function store(EmployeeStoreRequest $request)
    {
        $validated = $request->validated();

        $password = $validated['password'];
        unset($validated['password']);

        $nik = 'EMP'.str_pad((string) (Employee::max('id') + 1), 5, '0', STR_PAD_LEFT);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('employees', 'tenant');
        }

        $employee = DB::transaction(function () use ($validated, $nik, $password) {
            $employee = Employee::create(array_merge($validated, [
                'nik' => $nik,
                'is_active' => true,
            ]));

            User::create([
                'name' => $employee->name,
                'email' => $validated['email_personal'],
                'password' => $password,
                'role' => 'employee',
                'employee_id' => $employee->id,
                'tenant_id' => tenant('id'),
                'is_active' => true,
            ]);

            return $employee;
        });

        ActivityLog::log('employee.created', Employee::class, $employee->id, "Employee {$employee->name} created");

        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'position']);

        return Inertia::render('Employees/Show', [
            'employee' => $employee,
        ]);
    }

    public function edit(Employee $employee)
    {
        return Inertia::render('Employees/Edit', [
            'employee' => $employee,
            'departments' => Department::orderBy('name')->get(),
            'positions' => Position::orderBy('name')->get(),
        ]);
    }

    public function update(EmployeeUpdateRequest $request, Employee $employee)
    {
        $validated = $request->validated();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($employee->photo) {
                Storage::disk('tenant')->delete($employee->photo);
            }
            $validated['photo'] = $request->file('photo')->store('employees', 'tenant');
        }

        $employee->update($validated);

        ActivityLog::log('employee.updated', Employee::class, $employee->id, "Employee {$employee->name} updated");

        return redirect()->route('employees.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $name = $employee->name;
        $employee->delete();

        ActivityLog::log('employee.deleted', Employee::class, $employee->id, "Employee {$name} deleted");

        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }
}
