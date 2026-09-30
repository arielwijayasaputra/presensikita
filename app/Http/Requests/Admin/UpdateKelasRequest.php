<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKelasRequest extends FormRequest
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
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kelas', 'nama_kelas')->ignore($this->route('id'), 'id_kelas')->whereNull('deleted_at'),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique'   => 'Nama kelas sudah ada, tidak boleh duplikat.',
        ];
    }
}
