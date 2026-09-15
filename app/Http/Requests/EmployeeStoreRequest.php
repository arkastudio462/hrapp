<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name
 * @property string|null $email_personal
 * @property string|null $phone
 * @property string|null $birth_date
 * @property string|null $gender
 * @property string|null $address
 * @property int $department_id
 * @property int $position_id
 * @property string $join_date
 * @property string $status
 */
class EmployeeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email_personal' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:500',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'join_date' => 'required|date',
            'status' => 'required|in:contract,permanent,probation',
            'photo' => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email_personal.email' => 'Format email tidak valid.',
            'phone.string' => 'Nomor telepon harus berupa teks.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'birth_date.date' => 'Tanggal lahir tidak valid.',
            'gender.in' => 'Jenis kelamin harus berupa male atau female.',
            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat maksimal 500 karakter.',
            'department_id.required' => 'Departemen wajib dipilih.',
            'department_id.exists' => 'Departemen yang dipilih tidak valid.',
            'position_id.required' => 'Jabatan wajib dipilih.',
            'position_id.exists' => 'Jabatan yang dipilih tidak valid.',
            'join_date.required' => 'Tanggal masuk wajib diisi.',
            'join_date.date' => 'Tanggal masuk tidak valid.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status harus berupa contract, permanent, atau probation.',
            'photo.image' => 'Foto harus berupa file gambar.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
