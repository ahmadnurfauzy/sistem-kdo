<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Entitas utama: Kendaraan Dinas Operasional (KDO)
     */
    public function up(): void
    {
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_polisi', 15)->unique();   // contoh: B 1234 ABC
            $table->string('merek');                        // contoh: Toyota
            $table->string('tipe');                         // contoh: Avanza
            $table->unsignedSmallInteger('tahun_produksi'); // contoh: 2021
            $table->string('warna');                        // contoh: Putih
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->foreignId('perangkat_daerah_id')
                ->constrained('perangkat_daerah')
                ->restrictOnDelete();
            $table->foreignId('pegawai_id')                 // penanggung jawab (nullable)
                ->nullable()
                ->constrained('pegawai')
                ->nullOnDelete();
            $table->timestamps();

            $table->index('kondisi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
