<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePerangkatDaerahRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // otorisasi role ditangani middleware 'admin' di route
    }

    /**
     * Aturan validasi saat menambah perangkat daerah.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'max:20', 'unique:perangkat_daerah,kode'],
            'nama' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode.required' => 'Kode perangkat daerah wajib diisi.',
            'kode.unique' => 'Kode perangkat daerah sudah dipakai.',
            'kode.max' => 'Kode maksimal :max karakter.',
            'nama.required' => 'Nama perangkat daerah wajib diisi.',
            'nama.max' => 'Nama maksimal :max karakter.',
        ];
    }
}
