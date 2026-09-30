<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKelasRequest extends FormRequest
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
            'nama_kelas'    => [
                'required',
                'string',
                'max:50',
                Rule::unique('kelas', 'nama_kelas')->whereNull('deleted_at'),
            ],
            'tingkat_kelas' => 'required|string',
            'jurusan'       => 'required|string',
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
            'tingkat_kelas.required' => 'Tingkat kelas wajib dipilih.',
            'jurusan.required'    => 'Jurusan wajib dipilih.',
        ];
    }
}
