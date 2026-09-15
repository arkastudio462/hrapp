<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tenant = Tenant::with('package')->find($user->tenant_id);

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

        $newThisMonth = Employee::where('is_active', true)
            ->whereDate('join_date', '>=', now()->startOfMonth())
            ->count();

        $expiringContracts = Employee::where('is_active', true)
            ->where('status', 'contract')
            ->whereNotNull('join_date')
            ->whereBetween('join_date', [now()->subYears(1)->addDay(), now()->subYears(1)->addDays(30)])
            ->count();

        $latestPayroll = Payroll::select('net_salary')
            ->where('status', 'completed')
            ->get();
        $payrollEstimate = $latestPayroll->sum('net_salary');

        $currentMonth = (int) now()->format('m');
        $currentYear = (int) now()->format('Y');
        $currentPeriod = PayrollPeriod::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();
        $cutOffDay = $currentPeriod ? $currentPeriod->processed_at?->format('d M') : null;

        $settings = $tenant?->settings ?? [];
        $workStart = $settings['work_hours']['start'] ?? '08:00';
        $geofenceRadius = $settings['geofence_radius'] ?? 100;

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

        $departments = Department::withCount(['employees' => fn ($q) => $q->where('is_active', true)])
            ->get()
            ->map(function ($dept) {
                $totalEmployees = $dept->employees_count;

                $presentToday = Attendance::whereHas('employee', fn ($q) => $q->where('department_id', $dept->id)->where('is_active', true))
                    ->whereDate('date', today())
                    ->whereIn('status', ['present', 'late'])
                    ->count();

                $lateToday = Attendance::whereHas('employee', fn ($q) => $q->where('department_id', $dept->id)->where('is_active', true))
                    ->whereDate('date', today())
                    ->where('status', 'late')
                    ->count();

                $onLeave = LeaveRequest::whereHas('employee', fn ($q) => $q->where('department_id', $dept->id)->where('is_active', true))
                    ->where('status', 'approved')
                    ->whereDate('start_date', '<=', today())
                    ->whereDate('end_date', '>=', today())
                    ->count();

                $rate = $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 0) : 0;

                return [
                    'name' => $dept->name,
                    'total_employees' => $totalEmployees,
                    'present' => $presentToday,
                    'late' => $lateToday,
                    'on_leave' => $onLeave,
                    'rate' => $rate,
                    'detail' => "{$presentToday}/{$totalEmployees} Hadir"
                        .($lateToday > 0 ? " • {$lateToday} Terlambat" : '')
                        .($onLeave > 0 ? " • {$onLeave} Izin" : ''),
                    'good' => $rate >= 95,
                ];
            })
            ->sortByDesc('rate')
            ->values();

        $monthlyTrend = Attendance::select(
            DB::raw("strftime('%Y-%m', date) as month"),
            DB::raw("SUM(CASE WHEN status IN ('present', 'late') THEN 1 ELSE 0 END) as present_count"),
            DB::raw('COUNT(*) as total_count')
        )
            ->whereDate('date', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($row) use ($totalEmployees) {
                $rate = $totalEmployees > 0 && $row->total_count > 0
                    ? round(($row->present_count / $totalEmployees) * 100, 1)
                    : 0;

                $label = Carbon::parse($row->month.'-01')->locale('id')->isoFormat('MMM YY');

                return [
                    'label' => $label,
                    'rate' => $rate,
                ];
            });

        $maxEmployees = $tenant?->package?->max_employees ?? 200;

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalEmployees' => $totalEmployees,
                'presentToday' => $presentToday,
                'lateToday' => $lateToday,
                'onLeave' => $onLeave,
                'pendingApprovals' => $pendingApprovals,
                'permanentCount' => $permanentCount,
                'contractCount' => $contractCount,
                'newThisMonth' => $newThisMonth,
                'expiringContracts' => $expiringContracts,
                'attendanceRate' => $totalEmployees > 0
                    ? round(($presentToday / $totalEmployees) * 100, 1)
                    : 0,
                'payrollEstimate' => $payrollEstimate,
                'cutOffDay' => $cutOffDay,
            ],
            'subscription' => [
                'packageName' => $tenant?->package?->name ?? '-',
                'maxEmployees' => $maxEmployees,
                'subscriptionStatus' => $tenant?->subscription_status ?? 'trial',
            ],
            'settings' => [
                'workStart' => $workStart,
                'geofenceRadius' => $geofenceRadius,
            ],
            'pendingLeaves' => $pendingLeaves,
            'departments' => $departments,
            'monthlyTrend' => $monthlyTrend,
        ]);
    }
}
