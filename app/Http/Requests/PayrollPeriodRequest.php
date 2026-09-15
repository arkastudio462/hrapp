<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $month
 * @property int $year
 */
class PayrollPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
        ];
    }

    public function messages(): array
    {
        return [
            'month.required' => 'Bulan wajib diisi.',
            'month.integer' => 'Bulan harus berupa angka bulat.',
            'month.min' => 'Bulan minimal 1.',
            'month.max' => 'Bulan maksimal 12.',
            'year.required' => 'Tahun wajib diisi.',
            'year.integer' => 'Tahun harus berupa angka bulat.',
            'year.min' => 'Tahun minimal 2020.',
            'year.max' => 'Tahun maksimal 2030.',
        ];
    }
}
