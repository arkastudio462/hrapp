<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('tenant');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%");
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->status) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(15)->through(fn ($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'is_active' => $user->is_active,
            'tenant_name' => $user->tenant?->name ?? 'Super Admin',
            'last_login_at' => $user->last_login_at?->format('d M Y H:i'),
            'created_at' => $user->created_at->format('d M Y'),
        ]);

        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'super_admin' => User::where('role', 'super_admin')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'hr' => User::where('role', 'hr')->count(),
            'employee' => User::where('role', 'employee')->count(),
        ];

        return Inertia::render('SuperAdmin/Users/Index', [
            'users' => $users,
            'stats' => $stats,
            'filters' => $request->only(['search', 'role', 'status']),
        ]);
    }

    public function show(User $user)
    {
        $user->load('tenant');

        return Inertia::render('SuperAdmin/Users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_active' => $user->is_active,
                'tenant_name' => $user->tenant?->name ?? 'Super Admin',
                'last_login_at' => $user->last_login_at?->format('d M Y H:i'),
                'created_at' => $user->created_at->format('d M Y'),
            ],
        ]);
    }

    public function toggleActive(User $user)
    {
        if ($user->role === 'super_admin') {
            return back()->withErrors(['error' => 'Tidak dapat menonaktifkan akun Super Admin.']);
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', 'Status user berhasil diubah.');
    }

    public function resetPassword(User $user)
    {
        $user->update(['password' => Hash::make('password')]);

        return back()->with('success', 'Password user berhasil direset ke "password".');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'super_admin') {
            return back()->withErrors(['error' => 'Tidak dapat menghapus akun Super Admin.']);
        }

        $user->delete();

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
