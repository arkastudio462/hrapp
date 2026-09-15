<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeePortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $employee = Employee::where('id', $request->user()->employee_id)
            ->with(['department', 'position'])
            ->first();

        if (! $employee) {
            return inertia('EmployeePortal/Dashboard', [
                'employee' => null,
                'todayAttendance' => null,
                'leaveBalances' => [],
                'recentPayroll' => null,
            ]);
        }

        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', now()->toDateString())
            ->first();

        $leaveBalances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', now()->year)
            ->get();

        $recentPayroll = Payroll::where('employee_id', $employee->id)
            ->with('period')
            ->orderByDesc('created_at')
            ->first();

        return inertia('EmployeePortal/Dashboard', [
            'employee' => $employee,
            'todayAttendance' => $todayAttendance,
            'leaveBalances' => $leaveBalances,
            'recentPayroll' => $recentPayroll,
        ]);
    }

    public function attendance(Request $request)
    {
        $employee = Employee::where('id', $request->user()->employee_id)->first();

        if (! $employee) {
            return inertia('EmployeePortal/Attendance', [
                'attendances' => collect()->paginate(15),
            ]);
        }

        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderByDesc('date')
            ->paginate(15)
            ->through(fn ($att) => [
                'id' => $att->id,
                'date' => $att->date->format('d M Y'),
                'check_in_time' => $att->check_in_time?->format('H:i'),
                'check_out_time' => $att->check_out_time?->format('H:i'),
                'status' => $att->status,
            ]);

        return inertia('EmployeePortal/Attendance', [
            'attendances' => $attendances,
        ]);
    }

    public function leaves(Request $request)
    {
        $employee = Employee::where('id', $request->user()->employee_id)->first();

        if (! $employee) {
            return inertia('EmployeePortal/Leaves', [
                'leaves' => collect()->paginate(15),
                'balances' => [],
            ]);
        }

        $leaves = LeaveRequest::where('employee_id', $employee->id)
            ->orderByDesc('created_at')
            ->paginate(12)
            ->through(fn ($leave) => [
                'id' => $leave->id,
                'type' => $leave->type,
                'start_date' => $leave->start_date->format('d M Y'),
                'end_date' => $leave->end_date->format('d M Y'),
                'reason' => $leave->reason,
                'status' => $leave->status,
            ]);

        $balances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', now()->year)
            ->get();

        return inertia('EmployeePortal/Leaves', [
            'leaves' => $leaves,
            'balances' => $balances,
        ]);
    }

    public function createLeave(Request $request)
    {
        $employee = Employee::where('id', $request->user()->employee_id)
            ->with(['department', 'position'])
            ->first();

        return inertia('EmployeePortal/CreateLeave', [
            'employee' => $employee,
        ]);
    }

    public function storeLeave(Request $request)
    {
        $employee = Employee::where('id', $request->user()->employee_id)->first();

        if (! $employee) {
            return back()->withErrors(['employee' => 'Data karyawan tidak ditemukan.']);
        }

        $validated = $request->validate([
            'type' => 'required|in:leave,sick,permission',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('employee-portal.leaves')
            ->with('success', 'Pengajuan izin/cuti berhasil dikirim.');
    }

    public function payslips(Request $request)
    {
        $employee = Employee::where('id', $request->user()->employee_id)->first();

        if (! $employee) {
            return inertia('EmployeePortal/Payslips', [
                'payslips' => collect()->paginate(12),
            ]);
        }

        $payslips = Payroll::where('employee_id', $employee->id)
            ->with('period')
            ->orderByDesc('created_at')
            ->paginate(12);

        return inertia('EmployeePortal/Payslips', [
            'payslips' => $payslips,
        ]);
    }

    public function payslip(Request $request, Payroll $payroll)
    {
        $employee = Employee::where('id', $request->user()->employee_id)->first();

        if (! $employee || $payroll->employee_id !== $employee->id) {
            abort(403, 'Anda tidak memiliki akses ke payslip ini.');
        }

        $payroll->load(['employee.department', 'employee.position', 'items.component', 'period']);

        return Inertia::render('Payroll/Payslip', [
            'payroll' => $payroll,
        ]);
    }

    public function profile(Request $request)
    {
        $employee = Employee::where('id', $request->user()->employee_id)
            ->with(['department', 'position'])
            ->first();

        if ($employee) {
            $employee->birth_date = $employee->birth_date?->format('d M Y');
            $employee->join_date = $employee->join_date?->format('d M Y');
        }

        return inertia('EmployeePortal/Profile', [
            'employee' => $employee,
        ]);
    }
}
