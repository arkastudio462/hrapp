<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LeaveStoreRequest;
use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveRequestNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::with('employee');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('Leaves/Index', [
            'leaveRequests' => $leaveRequests,
            'filters' => $request->only(['status', 'type']),
        ]);
    }

    public function create()
    {
        $employee = Employee::where('user_id', auth()->id())->first();

        return Inertia::render('Leaves/Create', [
            'employee' => $employee,
            'balances' => $employee ? LeaveBalance::where('employee_id', $employee->id)
                ->where('year', now()->year)
                ->get() : collect(),
        ]);
    }

    public function store(LeaveStoreRequest $request)
    {
        $validated = $request->validated();

        $employee = Employee::where('user_id', auth()->id())->first();

        if (! $employee) {
            return back()->withErrors(['employee' => 'Data karyawan tidak ditemukan.']);
        }

        $leaveRequest = LeaveRequest::create(array_merge($validated, [
            'employee_id' => $employee->id,
            'status' => 'pending',
        ]));

        ActivityLog::log('leave.created', LeaveRequest::class, $leaveRequest->id, "Leave {$validated['type']} request created for {$employee->name}");

        $hrUsers = User::where('role', 'hr')->get();
        foreach ($hrUsers as $hrUser) {
            $hrUser->notify(new LeaveRequestNotification($leaveRequest, 'submitted'));
        }

        return redirect()->route('leaves.index')
            ->with('success', 'Pengajuan izin/cuti berhasil dikirim.');
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        if ($leaveRequest->type === 'leave') {
            $days = Carbon::parse($leaveRequest->start_date)
                ->diffInDays($leaveRequest->end_date) + 1;

            $balance = LeaveBalance::where('employee_id', $leaveRequest->employee_id)
                ->where('year', now()->year)
                ->where('type', 'annual')
                ->first();

            if ($balance) {
                $balance->increment('used', $days);
                $balance->decrement('remaining', $days);
            }
        }

        $employee = $leaveRequest->employee;
        if ($employee && $employee->user) {
            $employee->user->notify(new LeaveRequestNotification($leaveRequest, 'approved'));
        }

        ActivityLog::log('leave.approved', LeaveRequest::class, $leaveRequest->id, "Leave {$leaveRequest->type} approved for {$employee->name}");

        return back()->with('success', 'Pengajuan izin/cuti disetujui.');
    }

    public function reject(LeaveRequest $leaveRequest)
    {
        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $employee = $leaveRequest->employee;
        if ($employee && $employee->user) {
            $employee->user->notify(new LeaveRequestNotification($leaveRequest, 'rejected'));
        }

        ActivityLog::log('leave.rejected', LeaveRequest::class, $leaveRequest->id, "Leave {$leaveRequest->type} rejected for {$employee->name}");

        return back()->with('success', 'Pengajuan izin/cuti ditolak.');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        $leaveRequest->delete();

        return back()->with('success', 'Pengajuan izin/cuti berhasil dihapus.');
    }
}
