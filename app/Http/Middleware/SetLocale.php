<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menyesuaikan bahasa pesan (validasi, notifikasi, tanggal) dengan bahasa yang
 * dipilih pengguna di halaman Akun. Pilihan disimpan browser di localStorage,
 * lalu disalin ke cookie "beres_lang" oleh partial pref-head / locale-cookie.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $bahasa = $request->cookie('beres_lang');
        $locale = in_array($bahasa, ['id', 'en'], true) ? $bahasa : 'id';

        App::setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}