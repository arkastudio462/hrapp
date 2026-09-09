<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request)
    {
        $totalEmployees = Employee::where('is_active', true)->count();

        $todayAttendance = Attendance::whereDate('date', today())->get();

        $presentToday = $todayAttendance->whereIn('status', ['present', 'late'])->count();
        $lateToday = $todayAttendance->where('status', 'late')->count();

        $onLeave = LeaveRequest::where('status', 'approved')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->count();

        $pendingApprovals = LeaveRequest::where('status', 'pending')->count();

        $permanentCount = Employee::where('is_active', true)->where('status', 'permanent')->count();
        $contractCount = Employee::where('is_active', true)->whereIn('status', ['contract', 'probation'])->count();

        $pendingLeaves = LeaveRequest::with('employee:id,name,position_id,department_id')
            ->with('employee.position:id,name')
            ->with('employee.department:id,name')
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn (LeaveRequest $leave) => [
                'id' => $leave->id,
                'employee_name' => $leave->employee?->name,
                'position' => $leave->employee?->position?->name,
                'department' => $leave->employee?->department?->name,
                'type' => $leave->type,
                'start_date' => $leave->start_date?->format('d M Y'),
                'end_date' => $leave->end_date?->format('d M Y'),
                'days' => $leave->start_date && $leave->end_date
                    ? $leave->start_date->diffInDays($leave->end_date) + 1
                    : 0,
                'reason' => $leave->reason,
            ]);

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalEmployees' => $totalEmployees,
                'presentToday' => $presentToday,
                'lateToday' => $lateToday,
                'onLeave' => $onLeave,
                'pendingApprovals' => $pendingApprovals,
                'permanentCount' => $permanentCount,
                'contractCount' => $contractCount,
                'attendanceRate' => $totalEmployees > 0
                    ? round(($presentToday / $totalEmployees) * 100, 1)
                    : 0,
            ],
            'pendingLeaves' => $pendingLeaves,
        ]);
    }
}
