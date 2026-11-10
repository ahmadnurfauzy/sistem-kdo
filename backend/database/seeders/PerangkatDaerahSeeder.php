<?php

namespace Database\Seeders;

use App\Models\PerangkatDaerah;
use Illuminate\Database\Seeder;

class PerangkatDaerahSeeder extends Seeder
{
    /**
     * Data perangkat daerah (instansi) — contoh realistis.
     */
    public function run(): void
    {
        $data = [
            ['kode' => 'SETDA', 'nama' => 'Sekretariat Daerah'],
            ['kode' => 'DISDIK', 'nama' => 'Dinas Pendidikan'],
            ['kode' => 'DINKES', 'nama' => 'Dinas Kesehatan'],
            ['kode' => 'DISHUB', 'nama' => 'Dinas Perhubungan'],
            ['kode' => 'DPUPR', 'nama' => 'Dinas Pekerjaan Umum dan Penataan Ruang'],
            ['kode' => 'BKD', 'nama' => 'Badan Kepegawaian Daerah'],
        ];

        foreach ($data as $item) {
            PerangkatDaerah::create($item);
        }
    }
}
