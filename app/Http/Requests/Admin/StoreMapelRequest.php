<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMapelRequest extends FormRequest
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
            'nama_mapel' => 'required|string|max:100',
            'kode_mapel' => 'nullable|string|max:20|unique:mapel,kode_mapel',
            'kelompok' => 'nullable|string|in:A,B,C',
        ];
    }
}
