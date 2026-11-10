<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PegawaiSeeder extends Seeder
{
    /**
     * Data pegawai Negeri Sipil — NIP 18 digit mengikuti format resmi:
     * tanggal lahir (8) + TMT pengangkatan (6: YYYYMM) + jenis kelamin (1: 1=pria, 2=wanita) + nomor urut (3).
     */
    public function run(): void
    {
        $data = [
            // Sekretariat Daerah
            ['nama' => 'Budi Santoso', 'nip' => '19750412 199803 1 001', 'jabatan' => 'Kepala Bagian Umum', 'golongan' => 'IV/b', 'kode_pd' => 'SETDA'],
            ['nama' => 'Siti Rahmawati', 'nip' => '19800923 200504 2 002', 'jabatan' => 'Kepala Sub Bagian Kepegawaian', 'golongan' => 'III/d', 'kode_pd' => 'SETDA'],

            // Dinas Pendidikan
            ['nama' => 'Ahmad Hidayat', 'nip' => '19681105 199403 1 004', 'jabatan' => 'Kepala Dinas Pendidikan', 'golongan' => 'IV/c', 'kode_pd' => 'DISDIK'],
            ['nama' => 'Dewi Lestari', 'nip' => '19900117 201503 2 007', 'jabatan' => 'Pengelola Barang Milik Daerah', 'golongan' => 'III/a', 'kode_pd' => 'DISDIK'],
            ['nama' => 'Rizki Pratama', 'nip' => '19850630 201012 1 003', 'jabatan' => 'Kepala Sub Bagian Sarana Prasarana', 'golongan' => 'III/c', 'kode_pd' => 'DISDIK'],

            // Dinas Kesehatan
            ['nama' => 'Endang Kusmiati', 'nip' => '19720815 199902 2 001', 'jabatan' => 'Kepala Dinas Kesehatan', 'golongan' => 'IV/b', 'kode_pd' => 'DINKES'],
            ['nama' => 'Agus Wibowo', 'nip' => '19930328 201810 1 002', 'jabatan' => 'Analis Kesehatan', 'golongan' => 'III/a', 'kode_pd' => 'DINKES'],

            // Dinas Perhubungan
            ['nama' => 'Hendra Gunawan', 'nip' => '19790709 200604 1 005', 'jabatan' => 'Kepala Bidang Angkutan Darat', 'golongan' => 'IV/a', 'kode_pd' => 'DISHUB'],
            ['nama' => 'Maya Puspita', 'nip' => '19881201 201306 2 001', 'jabatan' => 'Pengadministrasi Perhubungan', 'golongan' => 'III/b', 'kode_pd' => 'DISHUB'],

            // Dinas Pekerjaan Umum
            ['nama' => 'Slamet Riyadi', 'nip' => '19740220 200001 1 006', 'jabatan' => 'Kepala Bidang Bina Marga', 'golongan' => 'IV/a', 'kode_pd' => 'DPUPR'],
            ['nama' => 'Fitri Handayani', 'nip' => '19920516 201611 2 003', 'jabatan' => 'Pengelola Keuangan', 'golongan' => 'III/a', 'kode_pd' => 'DPUPR'],

            // Badan Kepegawaian Daerah
            ['nama' => 'Taufik Hidayat', 'nip' => '19810311 200809 1 004', 'jabatan' => 'Kepala Badan Kepegawaian Daerah', 'golongan' => 'IV/b', 'kode_pd' => 'BKD'],
        ];

        foreach ($data as $item) {
            $pd = \App\Models\PerangkatDaerah::where('kode', $item['kode_pd'])->first();

            Pegawai::create([
                'nama' => $item['nama'],
                'nip' => str_replace(' ', '', $item['nip']),
                'jabatan' => $item['jabatan'],
                'golongan' => $item['golongan'],
                'perangkat_daerah_id' => $pd->id,
            ]);
        }
    }
}
