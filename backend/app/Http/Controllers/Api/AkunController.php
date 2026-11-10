<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAkunRequest;
use App\Http\Requests\UpdateAkunRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AkunController extends Controller
{
    /**
     * Daftar akun pengguna
     *
     * Daftar akun sistem dengan pencarian (nama/email), filter role, dan paginasi. Hanya admin.
     *
     * @group Akun (Kelola Pengguna)
     *
     * @queryParam search string Opsional, cari berdasarkan nama atau email. Example: pengelola
     * @queryParam role string Opsional, filter role: admin, pengelola, pengguna. Example: pengelola
     * @queryParam per_page int Opsional, jumlah item per halaman (maks 100). Example: 15
     *
     * @response 200 scenario="sukses" {"data":[{"id":1,"name":"Administrator KDO","email":"admin@kdo.test","role":"admin","dibuat_pada":"2026-11-10 06:00:00","diubah_pada":"2026-11-10 06:00:00"}],"meta":{"current_page":1,"from":1,"last_page":1,"per_page":15,"to":3,"total":3}}
     */
    public function index(): AnonymousResourceCollection
    {
        $perPage = (int) request('per_page', 15);

        $query = User::query();

        if ($search = request('search')) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        if ($role = request('role')) {
            $query->where('role', $role);
        }

        return UserResource::collection(
            $query->orderBy('name')->paginate($perPage > 100 ? 100 : $perPage)
        );
    }

    /**
     * Tambah akun pengguna
     *
     * Membuat akun baru dengan role admin, pengelola, atau pengguna. Hanya admin.
     *
     * @group Akun (Kelola Pengguna)
     *
     * @bodyParam name string required Nama pengguna. Example: Budi Pengelola
     * @bodyParam email string required Email unik. Example: budi@kdo.test
     * @bodyParam password string required Kata sandi (min. 8 karakter). Example: password123
     * @bodyParam role string required Role: admin, pengelola, atau pengguna. Example: pengelola
     *
     * @response 201 scenario="sukses" {"data":{"id":4,"name":"Budi Pengelola","email":"budi@kdo.test","role":"pengelola","dibuat_pada":"2026-11-10 06:10:00","diubah_pada":"2026-11-10 06:10:00"}}
     * @response 422 scenario="email duplikat" {"message":"Email sudah terdaftar.","errors":{"email":["Email sudah terdaftar."]}}
     * @response 403 scenario="bukan admin" {"message":"Akses ditolak. Hanya admin yang boleh mengelola akun."}
     */
    public function store(StoreAkunRequest $request): JsonResponse
    {
        // password otomatis ter-hash oleh cast 'hashed' di model User
        $akun = User::create($request->validated());

        return (new UserResource($akun))
            ->response()
            ->setStatusCode(201)
            ->header('Location', route('akun.show', $akun));
    }

    /**
     * Detail akun pengguna
     *
     * Mengambil satu data akun berdasarkan ID. Hanya admin.
     *
     * @group Akun (Kelola Pengguna)
     *
     * @urlParam id int required ID akun. Example: 1
     *
     * @response 200 {"data":{"id":1,"name":"Administrator KDO","email":"admin@kdo.test","role":"admin","dibuat_pada":"2026-11-10 06:00:00","diubah_pada":"2026-11-10 06:00:00"}}
     * @response 404 scenario="tidak ditemukan" {"message":"Data tidak ditemukan."}
     */
    public function show(User $akun): UserResource
    {
        return new UserResource($akun);
    }

    /**
     * Ubah akun pengguna
     *
     * Memperbarui nama, email, role, dan/atau kata sandi akun. Kata sandi
     * opsional — kosongkan bila tidak ingin mengubah. Role admin terakhir
     * tidak dapat diturunkan (409). Hanya admin.
     *
     * @group Akun (Kelola Pengguna)
     *
     * @urlParam id int required ID akun. Example: 2
     *
     * @bodyParam name string required Nama pengguna. Example: Pengelola KDO
     * @bodyParam email string required Email unik. Example: pengelola@kdo.test
     * @bodyParam password string Opsional, kata sandi baru (min. 8 karakter). Kosongkan bila tidak diubah. Example: passwordBaru
     * @bodyParam role string required Role: admin, pengelola, atau pengguna. Example: pengelola
     *
     * @response 200 scenario="sukses" {"data":{"id":2,"name":"Pengelola KDO","email":"pengelola@kdo.test","role":"pengelola","dibuat_pada":"2026-11-10 06:00:00","diubah_pada":"2026-11-10 06:15:00"}}
     * @response 409 scenario="admin terakhir diturunkan" {"message":"Tidak dapat menurunkan role admin terakhir. Tambahkan admin lain terlebih dahulu."}
     */
    public function update(UpdateAkunRequest $request, User $akun): UserResource
    {
        $data = $request->validated();

        if ($akun->role === 'admin'
            && $data['role'] !== 'admin'
            && User::where('role', 'admin')->count() <= 1) {
            abort(409, 'Tidak dapat menurunkan role admin terakhir. Tambahkan admin lain terlebih dahulu.');
        }

        // kata sandi opsional: hanya diubah bila diisi
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $akun->update($data);

        return new UserResource($akun->fresh());
    }

    /**
     * Hapus akun pengguna
     *
     * Menghapus akun beserta token aktifnya. Tidak dapat menghapus akun
     * yang sedang dipakai (409) atau admin terakhir (409). Hanya admin.
     *
     * @group Akun (Kelola Pengguna)
     *
     * @urlParam id int required ID akun. Example: 4
     *
     * @response 204 scenario="sukses"
     * @response 409 scenario="akun sendiri" {"message":"Tidak dapat menghapus akun yang sedang Anda gunakan."}
     * @response 409 scenario="admin terakhir" {"message":"Tidak dapat menghapus admin terakhir."}
     */
    public function destroy(User $akun): Response
    {
        if ($akun->id === request()->user()->id) {
            abort(409, 'Tidak dapat menghapus akun yang sedang Anda gunakan.');
        }

        if ($akun->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            abort(409, 'Tidak dapat menghapus admin terakhir.');
        }

        // cabut seluruh token milik akun agar sesi aktifnya langsung berakhir
        $akun->tokens()->delete();
        $akun->delete();

        return response()->noContent();
    }
}
