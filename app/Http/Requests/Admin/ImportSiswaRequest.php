<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportSiswaRequest extends FormRequest
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
            'file_csv' => 'required|file|max:25600',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file_csv.required' => 'File Excel / CSV wajib diupload.',
            'file_csv.max' => 'Ukuran file maksimal 25MB.',
        ];
    }
}
