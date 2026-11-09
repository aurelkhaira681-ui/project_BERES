<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gaya kartu standar BERES. Dipakai semua halaman lewat {{ $card }} dan <x-card>.
        // Ingin mengubah tampilan kartu di seluruh aplikasi? Cukup ubah satu baris ini.
        View::share('card', 'bg-white rounded-2xl border border-slate-100 shadow-[0_2px_14px_rgba(20,50,105,0.06)]');
    }
}