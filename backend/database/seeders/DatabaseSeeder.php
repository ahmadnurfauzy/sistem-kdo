<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan penting: perangkat daerah dulu (induk),
     * lalu pegawai, lalu kendaraan (yang mereferensikan keduanya).
     */
    public function run(): void
    {
        $this->call([
            PerangkatDaerahSeeder::class,
            PegawaiSeeder::class,
            KendaraanSeeder::class,
            UserSeeder::class,
        ]);
    }
}
