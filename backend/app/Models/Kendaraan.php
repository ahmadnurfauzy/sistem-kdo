<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kendaraan extends Model
{
    protected $table = 'kendaraan';

    protected $fillable = [
        'nomor_polisi',
        'merek',
        'tipe',
        'tahun_produksi',
        'warna',
        'kondisi',
        'perangkat_daerah_id',
        'pegawai_id',
    ];

    public function perangkatDaerah(): BelongsTo
    {
        return $this->belongsTo(PerangkatDaerah::class);
    }

    public function penanggungJawab(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
