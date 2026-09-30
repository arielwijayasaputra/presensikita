<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
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
            'nama_siswa'    => 'required|string|max:150',
            'nisn'          => 'required|string|max:20|unique:siswa,nisn',
            'id_kelas'      => 'required|exists:kelas,id_kelas',
            'jenis_kelamin' => 'required|in:L,P',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_siswa.required'    => 'Nama siswa wajib diisi.',
            'nisn.required'          => 'NISN wajib diisi.',
            'nisn.unique'            => 'NISN sudah digunakan oleh siswa lain.',
            'id_kelas.required'      => 'Kelas wajib dipilih.',
            'id_kelas.exists'        => 'Kelas yang dipilih tidak valid.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
        ];
    }
}
