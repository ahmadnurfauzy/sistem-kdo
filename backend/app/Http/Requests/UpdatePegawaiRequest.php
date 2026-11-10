<?php

namespace App\Http\Requests;

use App\Models\PerangkatDaerah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePegawaiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // otorisasi role ditangani middleware 'admin' di route
    }

    /**
     * Aturan validasi saat memperbarui pegawai (PNS).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('pegawai');

        return [
            'nama' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'digits:18', Rule::unique('pegawai', 'nip')->ignore($id)],
            'jabatan' => ['required', 'string', 'max:255'],
            'golongan' => ['required', 'string', 'regex:/^(?:I|II|III|IV)\/(?:a|b|c|d|e)$/'],
            'perangkat_daerah_id' => ['required', 'integer', Rule::exists(PerangkatDaerah::class, 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama pegawai wajib diisi.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.digits' => 'NIP harus tepat 18 digit angka.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'golongan.required' => 'Golongan wajib diisi.',
            'golongan.regex' => 'Format golongan tidak valid (contoh: III/a, IV/b).',
            'perangkat_daerah_id.required' => 'Perangkat daerah wajib dipilih.',
            'perangkat_daerah_id.exists' => 'Perangkat daerah tidak ditemukan.',
        ];
    }
}
