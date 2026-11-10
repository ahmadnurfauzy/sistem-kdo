<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerangkatDaerahResource extends JsonResource
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
            'kode' => $this->kode,
            'nama' => $this->nama,
            'jumlah_pegawai' => $this->whenCounted('pegawai'),
            'jumlah_kendaraan' => $this->whenCounted('kendaraan'),
            'dibuat_pada' => $this->created_at?->format('Y-m-d H:i:s'),
            'diubah_pada' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
