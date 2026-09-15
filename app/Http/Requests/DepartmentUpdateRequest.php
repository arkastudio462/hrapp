<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name
 * @property int|null $parent_id
 * @property float|null $budget
 */
class DepartmentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'parent_id' => 'sometimes|nullable|exists:departments,id',
            'budget' => 'sometimes|nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama departemen wajib diisi.',
            'name.string' => 'Nama departemen harus berupa teks.',
            'name.max' => 'Nama departemen maksimal 255 karakter.',
            'parent_id.exists' => 'Departemen induk yang dipilih tidak valid.',
            'budget.numeric' => 'Anggaran harus berupa angka.',
            'budget.min' => 'Anggaran tidak boleh kurang dari 0.',
        ];
    }
}
