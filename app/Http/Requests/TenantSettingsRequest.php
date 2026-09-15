<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string|null $company_name
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $npwp
 * @property string|null $work_start
 * @property string|null $work_end
 * @property int|null $late_tolerance
 * @property int|null $geofence_radius
 * @property int|null $annual_leave
 * @property int|null $sick_leave
 * @property float|null $office_latitude
 * @property float|null $office_longitude
 * @property string|null $attendance_method
 */
class TenantSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'npwp' => 'nullable|string|max:20',
            'work_start' => 'nullable|string',
            'work_end' => 'nullable|string',
            'late_tolerance' => 'nullable|integer|min:0',
            'geofence_radius' => 'nullable|integer|min:50|max:500',
            'annual_leave' => 'nullable|integer|min:0',
            'sick_leave' => 'nullable|integer|min:0',
            'office_latitude' => 'nullable|numeric|between:-90,90',
            'office_longitude' => 'nullable|numeric|between:-180,180',
            'attendance_method' => 'nullable|in:face_location,qr_only',
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.string' => 'Nama perusahaan harus berupa teks.',
            'company_name.max' => 'Nama perusahaan maksimal 255 karakter.',
            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat maksimal 500 karakter.',
            'phone.string' => 'Nomor telepon harus berupa teks.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'npwp.string' => 'NPWP harus berupa teks.',
            'npwp.max' => 'NPWP maksimal 20 karakter.',
            'work_start.string' => 'Jam kerja mulai harus berupa teks.',
            'work_end.string' => 'Jam kerja selesai harus berupa teks.',
            'late_tolerance.integer' => 'Toleransi keterlambatan harus berupa angka bulat.',
            'late_tolerance.min' => 'Toleransi keterlambatan tidak boleh kurang dari 0.',
            'geofence_radius.integer' => 'Radius geofence harus berupa angka bulat.',
            'geofence_radius.min' => 'Radius geofence minimal 50.',
            'geofence_radius.max' => 'Radius geofence maksimal 500.',
            'annual_leave.integer' => 'Jatah cuti tahunan harus berupa angka bulat.',
            'annual_leave.min' => 'Jatah cuti tahunan tidak boleh kurang dari 0.',
            'sick_leave.integer' => 'Jatah cuti sakit harus berupa angka bulat.',
            'sick_leave.min' => 'Jatah cuti sakit tidak boleh kurang dari 0.',
            'office_latitude.numeric' => 'Lintang kantor harus berupa angka.',
            'office_latitude.between' => 'Lintang kantor harus antara -90 dan 90.',
            'office_longitude.numeric' => 'Bujur kantor harus berupa angka.',
            'office_longitude.between' => 'Bujur kantor harus antara -180 dan 180.',
            'attendance_method.in' => 'Metode absensi harus berupa Wajah + Lokasi atau QR Only.',
        ];
    }
}
