<?php

namespace App\Http\Requests;

use App\Models\Pegawai;
use App\Models\PerangkatDaerah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKendaraanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // otorisasi role ditangani middleware 'admin' di route
    }

    /**
     * Aturan validasi saat memperbarui kendaraan dinas (KDO).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('kendaraan');

        return [
            'nomor_polisi' => ['required', 'string', 'max:15', 'regex:/^[A-Za-z]{1,2}\s?\d{1,4}\s?[A-Za-z]{0,3}$/', Rule::unique('kendaraan', 'nomor_polisi')->ignore($id)],
            'merek' => ['required', 'string', 'max:100'],
            'tipe' => ['required', 'string', 'max:100'],
            'tahun_produksi' => ['required', 'integer', 'between:1980,'.(now()->year + 1)],
            'warna' => ['required', 'string', 'max:50'],
            'kondisi' => ['required', Rule::in(['baik', 'rusak_ringan', 'rusak_berat'])],
            'perangkat_daerah_id' => ['required', 'integer', Rule::exists(PerangkatDaerah::class, 'id')],
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
            $pdId = $this->input('perangkat_daerah_id');

            if ($pegawaiId !== null && $pdId !== null) {
                $pegawai = Pegawai::find($pegawaiId);

                if ($pegawai && (int) $pegawai->perangkat_daerah_id !== (int) $pdId) {
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
            'nomor_polisi.required' => 'Nomor polisi wajib diisi.',
            'nomor_polisi.unique' => 'Nomor polisi sudah terdaftar.',
            'nomor_polisi.max' => 'Nomor polisi maksimal :max karakter.',
            'nomor_polisi.regex' => 'Format nomor polisi tidak valid (contoh: DK 1234 AB).',
            'merek.required' => 'Merek kendaraan wajib diisi.',
            'tipe.required' => 'Tipe/model kendaraan wajib diisi.',
            'tahun_produksi.required' => 'Tahun produksi wajib diisi.',
            'tahun_produksi.integer' => 'Tahun produksi harus angka.',
            'tahun_produksi.between' => 'Tahun produksi harus antara 1980 dan :max.',
            'warna.required' => 'Warna kendaraan wajib diisi.',
            'kondisi.required' => 'Kondisi kendaraan wajib dipilih.',
            'kondisi.in' => 'Kondisi harus salah satu dari: baik, rusak ringan, rusak berat.',
            'perangkat_daerah_id.required' => 'Perangkat daerah pemilik wajib dipilih.',
            'perangkat_daerah_id.exists' => 'Perangkat daerah tidak ditemukan.',
            'pegawai_id.exists' => 'Pegawai penanggung jawab tidak ditemukan.',
        ];
    }
}
