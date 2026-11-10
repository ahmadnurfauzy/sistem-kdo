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
     * GET /api/v1/perangkat-daerah
     * Daftar perangkat daerah: search (kode/nama) + paginasi.
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
     * POST /api/v1/perangkat-daerah
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
     * GET /api/v1/perangkat-daerah/{perangkat_daerah}
     */
    public function show(PerangkatDaerah $perangkatDaerah): PerangkatDaerahResource
    {
        $perangkatDaerah->loadCount(['pegawai', 'kendaraan']);

        return new PerangkatDaerahResource($perangkatDaerah);
    }

    /**
     * PUT/PATCH /api/v1/perangkat-daerah/{perangkat_daerah}
     */
    public function update(UpdatePerangkatDaerahRequest $request, PerangkatDaerah $perangkatDaerah): PerangkatDaerahResource
    {
        $perangkatDaerah->update($request->validated());

        return new PerangkatDaerahResource($perangkatDaerah->fresh());
    }

    /**
     * DELETE /api/v1/perangkat-daerah/{perangkat_daerah}
     * FK restrictOnDelete menjaga agar PD berisi data tidak bisa dihapus (409).
     */
    public function destroy(PerangkatDaerah $perangkatDaerah): Response
    {
        $perangkatDaerah->delete();

        return response()->noContent();
    }
}
