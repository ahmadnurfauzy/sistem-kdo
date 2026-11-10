<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Entitas utama: Perangkat Daerah (PD)
     * Instansi pengguna/pemilik kendaraan dinas.
     */
    public function up(): void
    {
        Schema::create('perangkat_daerah', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();      // contoh: DISDIK, DINKES
            $table->string('nama');                    // contoh: Dinas Pendidikan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perangkat_daerah');
    }
};
