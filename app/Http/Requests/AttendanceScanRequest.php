<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $code
 * @property int $employee_id
 */
class AttendanceScanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string',
            'employee_id' => 'required|exists:employees,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode QR wajib diisi.',
            'code.string' => 'Kode QR harus berupa teks.',
            'employee_id.required' => 'Karyawan wajib dipilih.',
            'employee_id.exists' => 'Karyawan yang dipilih tidak valid.',
        ];
    }
}
