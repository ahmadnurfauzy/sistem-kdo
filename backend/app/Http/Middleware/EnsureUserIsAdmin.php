<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Hanya role "admin" yang boleh melakukan aksi tulis (mutasi data).
     * Role lain (viewer) mendapat 403 dalam format JSON.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Akses ditolak. Hanya admin yang boleh melakukan aksi ini.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
