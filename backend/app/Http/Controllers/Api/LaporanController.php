<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerangkatDaerah;
use Illuminate\Http\JsonResponse;

class LaporanController extends Controller
{
    /**
     * Rekap kondisi kendaraan per perangkat daerah
     *
     * Laporan jumlah kendaraan tiap perangkat daerah menurut kondisi
     * (baik, rusak ringan, rusak berat) beserta ringkasan total keseluruhan.
     *
     * @group Laporan
     * @response 200 {"data":[{"perangkat_daerah":{"id":1,"kode":"SETDA","nama":"Sekretariat Daerah"},"total_kendaraan":2,"kondisi":{"baik":1,"rusak_ringan":1,"rusak_berat":0},"tanpa_penanggung_jawab":0}],"ringkasan":{"total_perangkat_daerah":6,"total_kendaraan":15,"total_baik":9,"total_rusak_ringan":4,"total_rusak_berat":2}}
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
