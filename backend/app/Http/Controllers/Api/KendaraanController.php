<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignPenanggungJawabRequest;
use App\Http\Requests\StoreKendaraanRequest;
use App\Http\Requests\UpdateKendaraanRequest;
use App\Http\Resources\KendaraanResource;
use App\Models\Kendaraan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class KendaraanController extends Controller
{
    /**
     * Daftar kendaraan (KDO)
     *
     * Daftar kendaraan dinas operasional dengan pencarian (nomor polisi/merek/tipe),
     * filter kondisi, filter perangkat daerah, dan paginasi.
     *
     * @group Kendaraan (KDO)
     * @queryParam search string Opsional, cari nomor polisi/merek/tipe. Example: avanza
     * @queryParam kondisi string Opsional, salah satu: baik, rusak_ringan, rusak_berat. Example: rusak_berat
     * @queryParam perangkat_daerah_id int Opsional, filter ID perangkat daerah. Example: 1
     * @queryParam per_page int Opsional, jumlah item per halaman (maks 100). Example: 15
     */
    public function index(): AnonymousResourceCollection
    {
        $perPage = (int) request('per_page', 15);

        $query = Kendaraan::query()
            ->with(['perangkatDaerah', 'penanggungJawab']);

        if ($search = request('search')) {
            $query->where(fn ($q) => $q
                ->where('nomor_polisi', 'like', "%{$search}%")
                ->orWhere('merek', 'like', "%{$search}%")
                ->orWhere('tipe', 'like', "%{$search}%"));
        }

        if ($kondisi = request('kondisi')) {
            $query->where('kondisi', $kondisi);
        }

        if ($pdId = request('perangkat_daerah_id')) {
            $query->where('perangkat_daerah_id', $pdId);
        }

        return KendaraanResource::collection(
            $query->latest('id')->paginate($perPage > 100 ? 100 : $perPage)
        );
    }

    /**
     * Tambah kendaraan (KDO)
     *
     * Membuat data kendaraan dinas baru. Hanya admin. Penanggung jawab (opsional)
     * harus pegawai dari perangkat daerah yang sama.
     *
     * @group Kendaraan (KDO)
     * @bodyParam nomor_polisi string required Nomor polisi unik (contoh: DK 1234 AB). Example: DK 7777 ZZ
     * @bodyParam merek string required Merek kendaraan. Example: Honda
     * @bodyParam tipe string required Tipe/model kendaraan. Example: Brio Satya E
     * @bodyParam tahun_produksi int required Tahun produksi (1980-kini). Example: 2023
     * @bodyParam warna string required Warna kendaraan. Example: Biru
     * @bodyParam kondisi string required Kondisi: baik, rusak_ringan, atau rusak_berat. Example: baik
     * @bodyParam perangkat_daerah_id int required ID perangkat daerah pemilik. Example: 2
     * @bodyParam pegawai_id int Opsional, ID pegawai penanggung jawab (satu PD). Example: 4
     * @response 201 scenario="sukses" {"data":{"id":16,"nomor_polisi":"DK 7777 ZZ","kondisi":"baik","kondisi_label":"Baik"}}
     * @response 422 scenario="PJ beda PD" {"message":"...","errors":{"pegawai_id":["Penanggung jawab harus pegawai dari perangkat daerah yang sama."]}}
     */
    public function store(StoreKendaraanRequest $request): JsonResponse
    {
        $kendaraan = Kendaraan::create($request->validated());

        return (new KendaraanResource(
            $kendaraan->load(['perangkatDaerah', 'penanggungJawab'])
        ))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Detail kendaraan (KDO)
     *
     * Mengambil satu data kendaraan berdasarkan ID, termasuk perangkat daerah
     * pemilik dan penanggung jawab.
     *
     * @group Kendaraan (KDO)
     * @urlParam id int required ID kendaraan. Example: 1
     */
    public function show(Kendaraan $kendaraan): KendaraanResource
    {
        return new KendaraanResource(
            $kendaraan->load(['perangkatDaerah', 'penanggungJawab'])
        );
    }

    /**
     * Ubah kendaraan (KDO)
     *
     * Memperbarui data kendaraan. Hanya admin.
     *
     * @group Kendaraan (KDO)
     * @urlParam id int required ID kendaraan. Example: 1
     */
    public function update(UpdateKendaraanRequest $request, Kendaraan $kendaraan): KendaraanResource
    {
        $kendaraan->update($request->validated());

        return new KendaraanResource(
            $kendaraan->fresh()->load(['perangkatDaerah', 'penanggungJawab'])
        );
    }

    /**
     * Hapus kendaraan (KDO)
     *
     * Menghapus data kendaraan. Hanya admin.
     *
     * @group Kendaraan (KDO)
     * @urlParam id int required ID kendaraan. Example: 1
     * @response 204 scenario="sukses"
     */
    public function destroy(Kendaraan $kendaraan): Response
    {
        $kendaraan->delete();

        return response()->noContent();
    }

    /**
     * Tetapkan penanggung jawab kendaraan
     *
     * Menetapkan, mengganti, atau melepas pegawai penanggung jawab sebuah kendaraan.
     * Hanya admin. Pegawai harus berasal dari perangkat daerah pemilik kendaraan.
     *
     * @group Kendaraan (KDO)
     * @urlParam id int required ID kendaraan. Example: 2
     * @bodyParam pegawai_id int Opsional, ID pegawai penanggung jawab; null untuk melepas penugasan. Example: 2
     * @response 200 scenario="sukses" {"data":{"id":2,"nomor_polisi":"DK 5678 CD","penanggung_jawab":{"id":2,"nama":"Siti Rahmawati"}}}
     * @response 422 scenario="PJ beda PD" {"message":"...","errors":{"pegawai_id":["Penanggung jawab harus pegawai dari perangkat daerah yang sama."]}}
     */
    public function assignPenanggungJawab(AssignPenanggungJawabRequest $request, Kendaraan $kendaraan): KendaraanResource
    {
        $kendaraan->update([
            'pegawai_id' => $request->validated()['pegawai_id'] ?? null,
        ]);

        return new KendaraanResource(
            $kendaraan->fresh()->load(['perangkatDaerah', 'penanggungJawab'])
        );
    }
}
