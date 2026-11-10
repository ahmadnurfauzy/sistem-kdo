<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Entitas utama: Pegawai Negeri Sipil (PNS)
     * Pegawai yang dapat menjadi penanggung jawab kendaraan dinas.
     */
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nip', 18)->unique();      // NIP 18 digit PNS
            $table->string('jabatan');
            $table->string('golongan', 10);           // contoh: III/a, IV/b
            $table->foreignId('perangkat_daerah_id')
                ->constrained('perangkat_daerah')
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
