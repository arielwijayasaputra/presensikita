<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DeleteSelectedGuruRequest extends FormRequest
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
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct', 'exists:guru,id_guru'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ids.required' => 'Pilih data guru yang akan dihapus.',
            'ids.min' => 'Pilih minimal satu data guru.',
            'ids.*.exists' => 'Sebagian data guru tidak ditemukan atau sudah dihapus.',
        ];
    }
}
