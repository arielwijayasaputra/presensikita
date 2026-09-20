<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportGuruRequest extends FormRequest
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
            'file_guru' => ['required', 'file', 'max:25600', 'mimes:csv,txt,xlsx,xls'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file_guru.required' => 'File data guru wajib dipilih.',
            'file_guru.mimes' => 'Format file harus CSV, Excel, atau TXT.',
            'file_guru.max' => 'Ukuran file maksimal 25MB.',
        ];
    }
}
