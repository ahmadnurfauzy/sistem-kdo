<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Akun uji tiga role: admin (CRUD data + kelola akun),
     * pengelola (CRUD data), pengguna (hanya baca).
     * Password sengaja sederhana untuk keperluan pengujian lokal.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator KDO',
            'email' => 'admin@kdo.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Pengelola KDO',
            'email' => 'pengelola@kdo.test',
            'password' => Hash::make('password123'),
            'role' => 'pengelola',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Pengguna KDO',
            'email' => 'pengguna@kdo.test',
            'password' => Hash::make('password123'),
            'role' => 'pengguna',
            'email_verified_at' => now(),
        ]);
    }
}
