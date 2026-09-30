<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuruRequest extends FormRequest
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
            'nip'       => 'nullable|string|max:30|unique:guru,nip',
            'username'  => 'required|string|unique:guru,username',
            'password'  => 'required|string|min:4',
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
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 4 karakter.',
        ];
    }
}
