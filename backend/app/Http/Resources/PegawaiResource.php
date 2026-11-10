<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PegawaiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'nip' => $this->nip,
            'jabatan' => $this->jabatan,
            'golongan' => $this->golongan,
            'perangkat_daerah' => $this->whenLoaded('perangkatDaerah', fn () => [
                'id' => $this->perangkatDaerah->id,
                'kode' => $this->perangkatDaerah->kode,
                'nama' => $this->perangkatDaerah->nama,
            ]),
            'jumlah_kendaraan_ditangani' => $this->whenCounted('kendaraan'),
            'dibuat_pada' => $this->created_at?->format('Y-m-d H:i:s'),
            'diubah_pada' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
