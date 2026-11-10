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
     * GET /api/v1/kendaraan
     * Daftar KDO: search (nomor polisi/merek/tipe) + filter kondisi
     * + filter perangkat daerah + paginasi. Eager loading menghindari N+1.
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
     * POST /api/v1/kendaraan
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
     * GET /api/v1/kendaraan/{kendaraan}
     */
    public function show(Kendaraan $kendaraan): KendaraanResource
    {
        return new KendaraanResource(
            $kendaraan->load(['perangkatDaerah', 'penanggungJawab'])
        );
    }

    /**
     * PUT/PATCH /api/v1/kendaraan/{kendaraan}
     */
    public function update(UpdateKendaraanRequest $request, Kendaraan $kendaraan): KendaraanResource
    {
        $kendaraan->update($request->validated());

        return new KendaraanResource(
            $kendaraan->fresh()->load(['perangkatDaerah', 'penanggungJawab'])
        );
    }

    /**
     * DELETE /api/v1/kendaraan/{kendaraan}
     */
    public function destroy(Kendaraan $kendaraan): Response
    {
        $kendaraan->delete();

        return response()->noContent();
    }

    /**
     * PUT /api/v1/kendaraan/{kendaraan}/penanggung-jawab
     * Menetapkan / mengganti / melepas penanggung jawab kendaraan.
     * Body: { "pegawai_id": 3 } atau { "pegawai_id": null } untuk melepas.
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
