<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Akun uji: admin (CRUD penuh) dan viewer (hanya baca).
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
            'name' => 'Pengamat KDO',
            'email' => 'viewer@kdo.test',
            'password' => Hash::make('password123'),
            'role' => 'viewer',
            'email_verified_at' => now(),
        ]);
    }
}
