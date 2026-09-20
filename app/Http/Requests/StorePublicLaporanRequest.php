<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicLaporanRequest extends FormRequest
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
            'role_pelapor' => 'required|string|max:50',
            'nama_pelapor' => 'required|string|max:100',
            'judul' => 'required|string|max:150',
            'isi_laporan' => 'required|string',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'role_pelapor.required' => 'Pilih peran/role Anda terlebih dahulu.',
            'nama_pelapor.required' => 'Nama pelapor wajib diisi.',
            'judul.required' => 'Judul laporan wajib diisi.',
            'isi_laporan.required' => 'Isi laporan wajib diisi.',
        ];
    }
}
