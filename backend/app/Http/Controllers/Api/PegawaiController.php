<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePegawaiRequest;
use App\Http\Requests\UpdatePegawaiRequest;
use App\Http\Resources\PegawaiResource;
use App\Models\Pegawai;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PegawaiController extends Controller
{
    /**
     * Daftar pegawai (PNS)
     *
     * Daftar pegawai negeri sipil dengan pencarian, filter perangkat daerah, dan paginasi.
     *
     * @group Pegawai (PNS)
     * @queryParam search string Opsional, cari berdasarkan nama/NIP/jabatan. Example: budi
     * @queryParam perangkat_daerah_id int Opsional, filter ID perangkat daerah. Example: 1
     * @queryParam per_page int Opsional, jumlah item per halaman (maks 100). Example: 15
     */
    public function index(): AnonymousResourceCollection
    {
        $perPage = (int) request('per_page', 15);

        $query = Pegawai::query()
            ->with('perangkatDaerah')
            ->withCount('kendaraan');

        if ($search = request('search')) {
            $query->where(fn ($q) => $q
                ->where('nama', 'like', "%{$search}%")
                ->orWhere('nip', 'like', "%{$search}%")
                ->orWhere('jabatan', 'like', "%{$search}%"));
        }

        if ($pdId = request('perangkat_daerah_id')) {
            $query->where('perangkat_daerah_id', $pdId);
        }

        return PegawaiResource::collection(
            $query->orderBy('nama')->paginate($perPage > 100 ? 100 : $perPage)
        );
    }

    /**
     * Tambah pegawai (PNS)
     *
     * Membuat data pegawai baru. Hanya admin. NIP harus 18 digit dan unik;
     * golongan format I/a sampai IV/e.
     *
     * @group Pegawai (PNS)
     * @bodyParam nama string required Nama lengkap pegawai. Example: Nur Aisyah
     * @bodyParam nip string required NIP 18 digit, unik. Example: 199410052017012005
     * @bodyParam jabatan string required Jabatan pegawai. Example: Analis Kepegawaian
     * @bodyParam golongan string required Golongan ruang (I/a-IV/e). Example: III/a
     * @bodyParam perangkat_daerah_id int required ID perangkat daerah tempat bertugas. Example: 1
     * @response 201 scenario="sukses" {"data":{"id":13,"nama":"Nur Aisyah","nip":"199410052017012005"}}
     * @response 422 scenario="NIP tidak 18 digit" {"message":"...","errors":{"nip":["NIP harus tepat 18 digit angka."]}}
     */
    public function store(StorePegawaiRequest $request): JsonResponse
    {
        $pegawai = Pegawai::create($request->validated());

        return (new PegawaiResource($pegawai->load('perangkatDaerah')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Detail pegawai (PNS)
     *
     * Mengambil satu data pegawai berdasarkan ID, termasuk perangkat daerah dan
     * jumlah kendaraan yang ditangani.
     *
     * @group Pegawai (PNS)
     * @urlParam id int required ID pegawai. Example: 1
     */
    public function show(Pegawai $pegawai): PegawaiResource
    {
        return new PegawaiResource(
            $pegawai->load('perangkatDaerah')->loadCount('kendaraan')
        );
    }

    /**
     * Ubah pegawai (PNS)
     *
     * Memperbarui data pegawai. Hanya admin.
     *
     * @group Pegawai (PNS)
     * @urlParam id int required ID pegawai. Example: 1
     */
    public function update(UpdatePegawaiRequest $request, Pegawai $pegawai): PegawaiResource
    {
        $pegawai->update($request->validated());

        return new PegawaiResource($pegawai->fresh()->load('perangkatDaerah'));
    }

    /**
     * Hapus pegawai (PNS)
     *
     * Menghapus data pegawai. Hanya admin. Kendaraan yang ditangani pegawai
     * tersebut otomatis menjadi tanpa penanggung jawab.
     *
     * @group Pegawai (PNS)
     * @urlParam id int required ID pegawai. Example: 1
     * @response 204 scenario="sukses"
     */
    public function destroy(Pegawai $pegawai): Response
    {
        $pegawai->delete();

        return response()->noContent();
    }
}
