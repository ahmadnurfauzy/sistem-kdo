<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login pengguna
     *
     * Login dengan email dan password untuk mendapatkan bearer token. Token
     * dipakai pada seluruh endpoint lain melalui header `Authorization: Bearer {token}`.
     *
     * @group Autentikasi
     * @unauthenticated
     * @bodyParam email string required Email pengguna. Example: admin@kdo.test
     * @bodyParam password string required Password pengguna (min. 8 karakter). Example: password123
     * @response 200 {"message":"Login berhasil.","data":{"token":"1|abc...","user":{"id":1,"name":"Administrator KDO","email":"admin@kdo.test","role":"admin"}}}
     * @response 422 {"message":"Email atau password salah.","errors":{"email":["Email atau password salah."]}}
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        // hapus token lama agar tidak menumpuk, lalu buat token baru
        $user->tokens()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ],
        ]);
    }

    /**
     * Logout pengguna
     *
     * Mencabut token yang sedang dipakai. Token tidak dapat digunakan lagi setelah logout.
     *
     * @group Autentikasi
     * @response 200 {"message":"Logout berhasil."}
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Profil pengguna aktif
     *
     * Mengembalikan profil pemilik token yang sedang dipakai.
     *
     * @group Autentikasi
     * @response 200 {"data":{"id":1,"name":"Administrator KDO","email":"admin@kdo.test","role":"admin"}}
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }
}
