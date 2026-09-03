<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use Illuminate\Http\Request;

class EmployeePortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $employee = Employee::where('user_id', $request->user()->id)
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
        $employee = Employee::where('user_id', $request->user()->id)->first();

        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderByDesc('date')
            ->paginate(15);

        return inertia('EmployeePortal/Attendance', [
            'attendances' => $attendances,
        ]);
    }

    public function leaves(Request $request)
    {
        $employee = Employee::where('user_id', $request->user()->id)->first();

        $leaves = LeaveRequest::where('employee_id', $employee->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        $balances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', now()->year)
            ->get();

        return inertia('EmployeePortal/Leaves', [
            'leaves' => $leaves,
            'balances' => $balances,
        ]);
    }

    public function payslips(Request $request)
    {
        $employee = Employee::where('user_id', $request->user()->id)->first();

        $payslips = Payroll::where('employee_id', $employee->id)
            ->with('period')
            ->orderByDesc('created_at')
            ->paginate(12);

        return inertia('EmployeePortal/Payslips', [
            'payslips' => $payslips,
        ]);
    }

    public function profile(Request $request)
    {
        $employee = Employee::where('user_id', $request->user()->id)
            ->with(['department', 'position'])
            ->first();

        return inertia('EmployeePortal/Profile', [
            'employee' => $employee,
        ]);
    }
}
