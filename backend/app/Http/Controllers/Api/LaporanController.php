<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerangkatDaerah;
use Illuminate\Http\JsonResponse;

class LaporanController extends Controller
{
    /**
     * GET /api/v1/laporan/kondisi
     * Rekap jumlah kendaraan per perangkat daerah menurut kondisi.
     */
    public function kondisi(): JsonResponse
    {
        $rekap = PerangkatDaerah::query()
            ->withCount([
                'kendaraan as total_kendaraan',
                'kendaraan as kondisi_baik' => fn ($q) => $q->where('kondisi', 'baik'),
                'kendaraan as kondisi_rusak_ringan' => fn ($q) => $q->where('kondisi', 'rusak_ringan'),
                'kendaraan as kondisi_rusak_berat' => fn ($q) => $q->where('kondisi', 'rusak_berat'),
                'kendaraan as tanpa_penanggung_jawab' => fn ($q) => $q->whereNull('pegawai_id'),
            ])
            ->orderBy('nama')
            ->get()
            ->map(fn ($pd) => [
                'perangkat_daerah' => [
                    'id' => $pd->id,
                    'kode' => $pd->kode,
                    'nama' => $pd->nama,
                ],
                'total_kendaraan' => (int) $pd->total_kendaraan,
                'kondisi' => [
                    'baik' => (int) $pd->kondisi_baik,
                    'rusak_ringan' => (int) $pd->kondisi_rusak_ringan,
                    'rusak_berat' => (int) $pd->kondisi_rusak_berat,
                ],
                'tanpa_penanggung_jawab' => (int) $pd->tanpa_penanggung_jawab,
            ]);

        return response()->json([
            'data' => $rekap,
            'ringkasan' => [
                'total_perangkat_daerah' => $rekap->count(),
                'total_kendaraan' => $rekap->sum('total_kendaraan'),
                'total_baik' => $rekap->sum(fn ($r) => $r['kondisi']['baik']),
                'total_rusak_ringan' => $rekap->sum(fn ($r) => $r['kondisi']['rusak_ringan']),
                'total_rusak_berat' => $rekap->sum(fn ($r) => $r['kondisi']['rusak_berat']),
            ],
        ]);
    }
}
