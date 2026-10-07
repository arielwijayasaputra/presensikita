<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDispenSiswaRequest extends FormRequest
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
            'id_siswa' => ['required', 'array', 'min:1'],
            'id_siswa.*' => ['required', 'integer', 'exists:siswa,id_siswa'],
            'tanggal_dispen' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_dispen'],
            'jenis_absen' => ['required', 'in:S,I,D'],
            'alasan' => ['nullable', 'string', 'max:2000'],
            'foto_surat' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('id_siswa')) {
            $raw = $this->input('id_siswa');
            if (!is_array($raw)) {
                $raw = [$raw];
            }
            $filtered = array_values(array_unique(array_filter($raw, fn($v) => !is_null($v) && $v !== '')));
            $this->merge(['id_siswa' => $filtered]);
        }
    }

    public function messages(): array
    {
        return [
            'id_siswa.required' => 'Pilih minimal satu siswa.',
            'id_siswa.min' => 'Pilih minimal satu siswa.',
            'id_siswa.*.exists' => 'Data siswa yang dipilih tidak valid.',
        ];
    }
}
