<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name
 * @property int $department_id
 * @property int $level
 * @property string|null $salary_grade
 */
class PositionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'department_id' => 'sometimes|required|exists:departments,id',
            'level' => 'sometimes|required|integer|min:1',
            'salary_grade' => 'sometimes|nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama jabatan wajib diisi.',
            'name.string' => 'Nama jabatan harus berupa teks.',
            'name.max' => 'Nama jabatan maksimal 255 karakter.',
            'department_id.required' => 'Departemen wajib dipilih.',
            'department_id.exists' => 'Departemen tidak valid.',
            'level.required' => 'Level wajib diisi.',
            'level.integer' => 'Level harus berupa angka bulat.',
            'level.min' => 'Level minimal 1.',
            'salary_grade.string' => 'Grade gaji harus berupa teks.',
            'salary_grade.max' => 'Grade gaji maksimal 50 karakter.',
        ];
    }
}
