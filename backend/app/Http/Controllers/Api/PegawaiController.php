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
     * GET /api/v1/pegawai
     * Daftar PNS: search (nama/nip/jabatan) + filter perangkat daerah + paginasi.
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
     * POST /api/v1/pegawai
     */
    public function store(StorePegawaiRequest $request): JsonResponse
    {
        $pegawai = Pegawai::create($request->validated());

        return (new PegawaiResource($pegawai->load('perangkatDaerah')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/v1/pegawai/{pegawai}
     */
    public function show(Pegawai $pegawai): PegawaiResource
    {
        return new PegawaiResource(
            $pegawai->load('perangkatDaerah')->loadCount('kendaraan')
        );
    }

    /**
     * PUT/PATCH /api/v1/pegawai/{pegawai}
     */
    public function update(UpdatePegawaiRequest $request, Pegawai $pegawai): PegawaiResource
    {
        $pegawai->update($request->validated());

        return new PegawaiResource($pegawai->fresh()->load('perangkatDaerah'));
    }

    /**
     * DELETE /api/v1/pegawai/{pegawai}
     * Pegawai yang menjadi PJ membuat kendaraan menjadi tanpa PJ (nullOnDelete).
     */
    public function destroy(Pegawai $pegawai): Response
    {
        $pegawai->delete();

        return response()->noContent();
    }
}
