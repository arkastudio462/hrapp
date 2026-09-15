<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $type
 * @property string $start_date
 * @property string $end_date
 * @property string $reason
 */
class LeaveStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:leave,sick,permission',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Tipe izin wajib dipilih.',
            'type.in' => 'Tipe izin harus berupa leave, sick, atau permission.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Tanggal mulai tidak valid.',
            'start_date.after_or_equal' => 'Tanggal mulai harus hari ini atau setelahnya.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Tanggal selesai tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'reason.required' => 'Alasan wajib diisi.',
            'reason.string' => 'Alasan harus berupa teks.',
            'reason.max' => 'Alasan maksimal 500 karakter.',
        ];
    }
}
