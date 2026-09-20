<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportJadwalRequest extends FormRequest
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
            'file_jadwal' => ['required', 'file', 'max:25600', 'mimes:csv,txt,xlsx,xls'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file_jadwal.required' => 'File jadwal mengajar wajib dipilih.',
            'file_jadwal.mimes' => 'Format file harus CSV, Excel, atau TXT.',
            'file_jadwal.max' => 'Ukuran file maksimal 25MB.',
        ];
    }
}
