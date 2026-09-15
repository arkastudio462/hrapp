<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentStoreRequest;
use App\Http\Requests\DepartmentUpdateRequest;
use App\Models\ActivityLog;
use App\Models\Department;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::with(['head', 'children']);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('type')) {
            match ($request->type) {
                'parent' => $query->whereNull('parent_id'),
                'child' => $query->whereNotNull('parent_id'),
                default => null,
            };
        }

        $departments = $query->orderBy('name')->paginate(15)->withQueryString();

        return Inertia::render('Departments/Index', [
            'departments' => $departments,
            'filters' => $request->only(['search', 'type']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Departments/Create', [
            'departments' => Department::whereNull('parent_id')->orderBy('name')->get(),
        ]);
    }

    public function store(DepartmentStoreRequest $request)
    {
        Department::create($request->validated());

        ActivityLog::log('department.created', Department::class, null, "Department {$request->validated('name')} created");

        return redirect()->route('departments.index')
            ->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function edit(Department $department)
    {
        return Inertia::render('Departments/Edit', [
            'department' => $department,
            'departments' => Department::whereNull('parent_id')
                ->where('id', '!=', $department->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(DepartmentUpdateRequest $request, Department $department)
    {
        $department->update($request->validated());

        ActivityLog::log('department.updated', Department::class, $department->id, "Department {$department->name} updated");

        return redirect()->route('departments.index')
            ->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Department $department)
    {
        $name = $department->name;
        $department->delete();

        ActivityLog::log('department.deleted', Department::class, $department->id, "Department {$name} deleted");

        return redirect()->route('departments.index')
            ->with('success', 'Departemen berhasil dihapus.');
    }
}
