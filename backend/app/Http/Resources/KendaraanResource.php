<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KendaraanResource extends JsonResource
{
    /**
     * Label ramah-baca untuk nilai enum kondisi.
     */
    public const KONDISI_LABEL = [
        'baik' => 'Baik',
        'rusak_ringan' => 'Rusak Ringan',
        'rusak_berat' => 'Rusak Berat',
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nomor_polisi' => $this->nomor_polisi,
            'merek' => $this->merek,
            'tipe' => $this->tipe,
            'tahun_produksi' => $this->tahun_produksi,
            'warna' => $this->warna,
            'kondisi' => $this->kondisi,
            'kondisi_label' => self::KONDISI_LABEL[$this->kondisi] ?? $this->kondisi,
            'perangkat_daerah' => $this->whenLoaded('perangkatDaerah', fn () => [
                'id' => $this->perangkatDaerah->id,
                'kode' => $this->perangkatDaerah->kode,
                'nama' => $this->perangkatDaerah->nama,
            ]),
            'penanggung_jawab' => $this->whenLoaded('penanggungJawab', fn () => $this->penanggungJawab ? [
                'id' => $this->penanggungJawab->id,
                'nama' => $this->penanggungJawab->nama,
                'nip' => $this->penanggungJawab->nip,
                'jabatan' => $this->penanggungJawab->jabatan,
                'golongan' => $this->penanggungJawab->golongan,
            ] : null),
            'dibuat_pada' => $this->created_at?->format('Y-m-d H:i:s'),
            'diubah_pada' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
