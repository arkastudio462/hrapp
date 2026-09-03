<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FaceAttendanceController extends Controller
{
    public function index()
    {
        $employee = Employee::where('user_id', auth()->id())->first();

        return Inertia::render('FaceAttendance/Index', [
            'employee' => $employee,
            'hasFaceData' => $employee && $employee->face_data,
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'face_data' => 'required|array|min:3',
            'face_data.*' => 'required|string',
        ]);

        $employee = Employee::where('user_id', auth()->id())->first();

        if (! $employee) {
            return back()->withErrors(['employee' => 'Data karyawan tidak ditemukan.']);
        }

        $faceDescriptors = [];
        foreach ($validated['face_data'] as $base64Image) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64=#i', '', $base64Image));
            $fileName = 'faces/'.$employee->nik.'_'.time().'_'.uniqid().'.jpg';
            Storage::disk('tenant')->put($fileName, $imageData);
            $faceDescriptors[] = $fileName;
        }

        $employee->update([
            'face_data' => $faceDescriptors,
        ]);

        return back()->with('success', 'Data wajah berhasil didaftarkan.');
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'photo' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $employee = Employee::where('user_id', auth()->id())->first();

        if (! $employee || ! $employee->face_data) {
            return back()->withErrors(['face' => 'Data wajah belum terdaftar.']);
        }

        $tenant = $request->user()->tenant;
        $settings = $tenant->settings ?? [];
        $officeLat = $settings['office_latitude'] ?? -6.2088;
        $officeLng = $settings['office_longitude'] ?? 106.8456;
        $geofenceRadius = $settings['geofence_radius'] ?? 100;

        $distance = $this->calculateDistance(
            $validated['latitude'],
            $validated['longitude'],
            $officeLat,
            $officeLng
        );

        if ($distance > $geofenceRadius) {
            return back()->withErrors([
                'geofence' => 'Anda berada di luar area geofence. Jarak: '.round($distance).'m dari kantor.',
            ]);
        }

        $today = now()->toDateString();
        $existing = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            if ($existing->check_out_time) {
                return back()->withErrors(['attendance' => 'Anda sudah melakukan check out hari ini.']);
            }

            $existing->update([
                'check_out_time' => now(),
                'check_in_photo' => $this->savePhoto($validated['photo'], $employee->nik, 'checkout'),
                'check_in_location' => [
                    'lat' => $validated['latitude'],
                    'lng' => $validated['longitude'],
                ],
            ]);

            return back()->with('success', 'Check out berhasil.');
        }

        $workStart = $settings['work_hours']['start'] ?? '08:00';
        $lateTolerance = $settings['late_tolerance_minutes'] ?? 15;

        $checkInTime = now();
        $workStartDateTime = now()->setTimeFromTimeString($workStart);
        $lateThreshold = $workStartDateTime->copy()->addMinutes($lateTolerance);

        $status = $checkInTime->gt($lateThreshold) ? 'late' : 'present';

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => $today,
            'check_in_time' => $checkInTime,
            'check_in_photo' => $this->savePhoto($validated['photo'], $employee->nik, 'checkin'),
            'check_in_location' => [
                'lat' => $validated['latitude'],
                'lng' => $validated['longitude'],
            ],
            'check_in_method' => 'face',
            'status' => $status,
        ]);

        return back()->with('success', 'Absensi berhasil. Status: '.($status === 'late' ? 'Terlambat' : 'Hadir'));
    }

    private function savePhoto(string $base64Photo, string $nik, string $type): string
    {
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64=#i', '', $base64Photo));
        $fileName = 'attendance/'.$nik.'_'.$type.'_'.now()->format('Y-m-d_His').'.jpg';
        Storage::disk('tenant')->put($fileName, $imageData);

        return $fileName;
    }

    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;

        $latFrom = deg2rad($lat1);
        $lngFrom = deg2rad($lng1);
        $latTo = deg2rad($lat2);
        $lngTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2)
            + cos($latFrom) * cos($latTo)
            * sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
