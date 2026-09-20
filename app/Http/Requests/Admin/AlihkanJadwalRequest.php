<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AlihkanJadwalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id_guru_tujuan' => ['required', 'integer', 'exists:guru,id_guru'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_guru_tujuan.required' => 'Pilih guru pengganti terlebih dahulu.',
            'id_guru_tujuan.exists' => 'Guru pengganti tidak ditemukan.',
        ];
    }
}
