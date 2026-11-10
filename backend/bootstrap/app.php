<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // alias middleware role: dipakai sebagai ->middleware('admin')
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // semua request API selalu dirender sebagai JSON
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        /*
         * Format error JSON konsisten untuk seluruh API: { "message": "..." }
         * - 401/403/404/429/500 memakai pesan ramah-baca, tanpa stack trace
         * - 409 untuk pelanggaran constraint FK (mis. hapus data berelasi)
         * - 422 validasi tetap memakai format bawaan { message, errors }
         */
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/*') || $e instanceof ValidationException) {
                return null; // biarkan penanganan default
            }

            if ($e instanceof QueryException && ($e->getCode() === '23000' || $e->getCode() === '23503')) {
                return response()->json([
                    'message' => 'Data tidak dapat diproses karena masih direferensikan oleh data lain.',
                ], 409);
            }

            $status = match (true) {
                $e instanceof AuthenticationException => 401,
                $e instanceof AuthorizationException => 403,
                $e instanceof ModelNotFoundException,
                $e instanceof NotFoundHttpException => 404,
                $e instanceof TooManyRequestsHttpException => 429,
                $e instanceof HttpExceptionInterface => $e->getStatusCode(),
                default => 500,
            };

            $message = match (true) {
                $e instanceof AuthenticationException => 'Tidak terautentikasi. Sertakan token yang valid.',
                $e instanceof AuthorizationException => 'Akses ditolak.',
                $e instanceof ModelNotFoundException,
                $e instanceof NotFoundHttpException => 'Data tidak ditemukan.',
                $e instanceof TooManyRequestsHttpException => 'Terlalu banyak permintaan. Silakan coba lagi nanti.',
                $e instanceof HttpExceptionInterface && $e->getMessage() !== '' => $e->getMessage(),
                $status === 405 => 'Metode HTTP tidak diizinkan untuk endpoint ini.',
                $status === 500 => 'Terjadi kesalahan pada server.',
                default => 'Terjadi kesalahan.',
            };

            return response()->json(['message' => $message], $status);
        });
    })->create();
