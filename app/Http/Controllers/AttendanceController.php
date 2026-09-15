<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceScanRequest;
use App\Http\Requests\AttendanceStoreRequest;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee');

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        } else {
            $query->where('date', now()->toDateString());
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('check_in_time', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('Attendances/Index', [
            'attendances' => $attendances,
            'employees' => Employee::where('is_active', true)->orderBy('name')->get(),
            'filters' => $request->only(['date', 'employee_id', 'status']),
        ]);
    }

    public function store(AttendanceStoreRequest $request)
    {
        $validated = $request->validated();

        $attendance = Attendance::updateOrCreate(
            ['employee_id' => $validated['employee_id'], 'date' => $validated['date']],
            $validated
        );

        return back()->with('success', 'Absensi berhasil dicatat.');
    }

    public function generateQr()
    {
        $today = now()->toDateString();

        $existing = QrCode::where('date', $today)->where('is_active', true)->first();
        if ($existing) {
            return response()->json($existing);
        }

        $qr = QrCode::create([
            'code' => Str::random(32),
            'date' => $today,
            'expires_at' => now()->addMinutes(5),
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        return response()->json($qr);
    }

    public function scanQr(AttendanceScanRequest $request)
    {
        $validated = $request->validated();

        $qr = QrCode::where('code', $validated['code'])
            ->where('date', now()->toDateString())
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

        if (! $qr) {
            return back()->withErrors(['code' => 'QR Code tidak valid atau sudah expired.']);
        }

        $alreadyUsed = Attendance::where('employee_id', $validated['employee_id'])
            ->where('date', now()->toDateString())
            ->exists();

        if ($alreadyUsed) {
            return back()->withErrors(['code' => 'Anda sudah melakukan absensi hari ini.']);
        }

        Attendance::create([
            'employee_id' => $validated['employee_id'],
            'date' => now()->toDateString(),
            'check_in_time' => now(),
            'check_in_method' => 'qr',
            'qr_code_id' => $qr->id,
            'status' => 'present',
        ]);

        return back()->with('success', 'Absensi berhasil.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return back()->with('success', 'Data absensi berhasil dihapus.');
    }

    public function exportAttendance(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $attendances = Attendance::with('employee')
            ->whereBetween('date', [$request->date_from, $request->date_to])
            ->orderBy('date')
            ->orderBy('employee_id')
            ->get();

        $filename = 'attendance_export_'.$request->date_from.'_'.$request->date_to.'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['NIK', 'Nama', 'Tanggal', 'Jam Masuk', 'Jam Keluar', 'Status', 'Metode']);

            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->employee->nik ?? '-',
                    $attendance->employee->name ?? '-',
                    $attendance->date ? $attendance->date->format('d/m/Y') : '-',
                    $attendance->check_in_time ? $attendance->check_in_time->format('H:i') : '-',
                    $attendance->check_out_time ? $attendance->check_out_time->format('H:i') : '-',
                    $attendance->status,
                    $attendance->check_in_method ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
