<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePerangkatDaerahRequest;
use App\Http\Requests\UpdatePerangkatDaerahRequest;
use App\Http\Resources\PerangkatDaerahResource;
use App\Models\PerangkatDaerah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PerangkatDaerahController extends Controller
{
    /**
     * Daftar perangkat daerah
     *
     * Daftar instansi dengan pencarian dan paginasi. Termasuk jumlah pegawai dan kendaraan tiap PD.
     *
     * @group Perangkat Daerah (PD)
     * @queryParam search string Opsional, cari berdasarkan kode/nama. Example: dinas
     * @queryParam per_page int Opsional, jumlah item per halaman (maks 100). Example: 15
     */
    public function index(): AnonymousResourceCollection
    {
        $perPage = (int) request('per_page', 15);

        $query = PerangkatDaerah::query()
            ->withCount(['pegawai', 'kendaraan']);

        if ($search = request('search')) {
            $query->where(fn ($q) => $q
                ->where('kode', 'like', "%{$search}%")
                ->orWhere('nama', 'like', "%{$search}%"));
        }

        return PerangkatDaerahResource::collection(
            $query->orderBy('kode')->paginate($perPage > 100 ? 100 : $perPage)
        );
    }

    /**
     * Tambah perangkat daerah
     *
     * Membuat data perangkat daerah baru. Hanya admin.
     *
     * @group Perangkat Daerah (PD)
     * @bodyParam kode string required Kode unik instansi (maks 20). Example: DISKOMINFO
     * @bodyParam nama string required Nama instansi. Example: Dinas Komunikasi dan Informatika
     * @response 201 scenario="sukses" {"data":{"id":7,"kode":"DISKOMINFO","nama":"Dinas Komunikasi dan Informatika"}}
     * @response 422 scenario="kode duplikat" {"message":"Kode perangkat daerah sudah dipakai.","errors":{"kode":["Kode perangkat daerah sudah dipakai."]}}
     */
    public function store(StorePerangkatDaerahRequest $request): JsonResponse
    {
        $pd = PerangkatDaerah::create($request->validated());

        return (new PerangkatDaerahResource($pd))
            ->response()
            ->setStatusCode(201)
            ->header('Location', route('perangkat-daerah.show', $pd));
    }

    /**
     * Detail perangkat daerah
     *
     * Mengambil satu data perangkat daerah berdasarkan ID.
     *
     * @group Perangkat Daerah (PD)
     * @urlParam id int required ID perangkat daerah. Example: 1
     * @response 404 scenario="tidak ditemukan" {"message":"Data tidak ditemukan."}
     */
    public function show(PerangkatDaerah $perangkatDaerah): PerangkatDaerahResource
    {
        $perangkatDaerah->loadCount(['pegawai', 'kendaraan']);

        return new PerangkatDaerahResource($perangkatDaerah);
    }

    /**
     * Ubah perangkat daerah
     *
     * Memperbarui data perangkat daerah. Hanya admin.
     *
     * @group Perangkat Daerah (PD)
     * @urlParam id int required ID perangkat daerah. Example: 1
     * @bodyParam kode string required Kode unik instansi. Example: SETDA
     * @bodyParam nama string required Nama instansi. Example: Sekretariat Daerah
     */
    public function update(UpdatePerangkatDaerahRequest $request, PerangkatDaerah $perangkatDaerah): PerangkatDaerahResource
    {
        $perangkatDaerah->update($request->validated());

        return new PerangkatDaerahResource($perangkatDaerah->fresh());
    }

    /**
     * Hapus perangkat daerah
     *
     * Menghapus data perangkat daerah. Hanya admin. PD yang masih memiliki
     * pegawai atau kendaraan tidak dapat dihapus (409).
     *
     * @group Perangkat Daerah (PD)
     * @urlParam id int required ID perangkat daerah. Example: 1
     * @response 204 scenario="sukses"
     * @response 409 scenario="masih berisi data" {"message":"Data tidak dapat diproses karena masih direferensikan oleh data lain."}
     */
    public function destroy(PerangkatDaerah $perangkatDaerah): Response
    {
        $perangkatDaerah->delete();

        return response()->noContent();
    }
}
