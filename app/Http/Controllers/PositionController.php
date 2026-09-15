<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PositionStoreRequest;
use App\Http\Requests\PositionUpdateRequest;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $query = Position::with('department');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $positions = $query->orderBy('level')->orderBy('name')->paginate(15)->withQueryString();

        return Inertia::render('Positions/Index', [
            'positions' => $positions,
            'departments' => Department::orderBy('name')->get(),
            'filters' => $request->only(['search', 'department_id']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Positions/Create', [
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function store(PositionStoreRequest $request)
    {
        Position::create($request->validated());

        ActivityLog::log('position.created', Position::class, null, "Position {$request->validated('name')} created");

        return redirect()->route('positions.index')
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit(Position $position)
    {
        return Inertia::render('Positions/Edit', [
            'position' => $position,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function update(PositionUpdateRequest $request, Position $position)
    {
        $position->update($request->validated());

        ActivityLog::log('position.updated', Position::class, $position->id, "Position {$position->name} updated");

        return redirect()->route('positions.index')
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position)
    {
        $name = $position->name;
        $position->delete();

        ActivityLog::log('position.deleted', Position::class, $position->id, "Position {$name} deleted");

        return redirect()->route('positions.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}
