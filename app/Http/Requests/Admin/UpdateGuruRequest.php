<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGuruRequest extends FormRequest
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
            'nama_guru' => 'required|string',
            'nip'       => ['nullable', 'string', 'max:30', Rule::unique('guru', 'nip')->ignore($this->route('id'), 'id_guru')],
            'username'  => ['required', 'string', Rule::unique('guru', 'username')->ignore($this->route('id'), 'id_guru')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_guru.required' => 'Nama guru wajib diisi.',
            'nip.unique'         => 'NIP sudah digunakan guru lain.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username sudah digunakan guru lain.',
        ];
    }
}
