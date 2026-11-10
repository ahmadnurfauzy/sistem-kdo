<?php

namespace App\Http\Requests;

use App\Models\Pegawai;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignPenanggungJawabRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // otorisasi role ditangani middleware 'admin' di route
    }

    /**
     * Aturan validasi saat menetapkan/mengganti penanggung jawab kendaraan.
     * pegawai_id = null berarti melepas penugasan (kendaraan tanpa PJ).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pegawai_id' => ['nullable', 'integer', Rule::exists(Pegawai::class, 'id')],
        ];
    }

    /**
     * Aturan bisnis tambahan: penanggung jawab harus pegawai dari
     * perangkat daerah yang sama dengan pemilik kendaraan.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $pegawaiId = $this->input('pegawai_id');
            $kendaraan = $this->route('kendaraan');

            if ($pegawaiId !== null && $kendaraan !== null) {
                $pegawai = Pegawai::find($pegawaiId);

                if ($pegawai && $pegawai->perangkat_daerah_id !== $kendaraan->perangkat_daerah_id) {
                    $validator->errors()->add(
                        'pegawai_id',
                        'Penanggung jawab harus pegawai dari perangkat daerah yang sama.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'pegawai_id.exists' => 'Pegawai penanggung jawab tidak ditemukan.',
        ];
    }
}
