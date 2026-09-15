<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $employee_id
 * @property string $date
 * @property string $check_in_time
 * @property string|null $check_out_time
 * @property string|null $check_in_method
 * @property string|null $notes
 */
class AttendanceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in_time' => 'required|date',
            'check_out_time' => 'nullable|date|after:check_in_time',
            'check_in_method' => 'nullable|in:face,qr',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Karyawan wajib dipilih.',
            'employee_id.exists' => 'Karyawan yang dipilih tidak valid.',
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Tanggal tidak valid.',
            'check_in_time.required' => 'Waktu check-in wajib diisi.',
            'check_in_time.date' => 'Waktu check-in tidak valid.',
            'check_out_time.date' => 'Waktu check-out tidak valid.',
            'check_out_time.after' => 'Waktu check-out harus setelah waktu check-in.',
            'check_in_method.in' => 'Metode check-in harus berupa face atau qr.',
            'notes.string' => 'Catatan harus berupa teks.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}
