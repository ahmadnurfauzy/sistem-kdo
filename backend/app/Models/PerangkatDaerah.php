<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerangkatDaerah extends Model
{
    protected $table = 'perangkat_daerah';

    protected $fillable = [
        'kode',
        'nama',
    ];

    public function pegawai(): HasMany
    {
        return $this->hasMany(Pegawai::class);
    }

    public function kendaraan(): HasMany
    {
        return $this->hasMany(Kendaraan::class);
    }
}
