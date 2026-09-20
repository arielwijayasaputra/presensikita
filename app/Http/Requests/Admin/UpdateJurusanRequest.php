<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJurusanRequest extends FormRequest
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
            'kode_jurusan' => ['required', 'string', 'max:20', Rule::unique('jurusan', 'kode_jurusan')->ignore($this->route('id'))],
            'nama_jurusan' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kode_jurusan.required' => 'Kode jurusan wajib diisi.',
            'kode_jurusan.unique' => 'Kode jurusan sudah digunakan.',
            'nama_jurusan.required' => 'Nama jurusan wajib diisi.',
        ];
    }
}
