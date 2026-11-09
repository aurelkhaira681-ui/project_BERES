<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FotoProfilController;
use App\Http\Controllers\Siswa\AduanController as SiswaAduanController;
use App\Http\Controllers\Siswa\AkunController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Admin\AduanController as AdminAduanController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AkunController as AdminAkunController;
use App\Http\Controllers\Admin\MasterDataController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Sudah login -> ke dashboard sesuai peran (mencegah loop redirect /login <-> /)
    if (auth()->check()) {
        return redirect()->route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'siswa.dashboard');
    }

    return redirect()->route('login');
});

// ===== AUTH =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/register/petugas', [AuthController::class, 'showRegisterPetugas'])->name('register.petugas');
    Route::post('/register/petugas', [AuthController::class, 'registerPetugas']);
    Route::get('/lupa-sandi', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/lupa-sandi', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.update');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ===== FOTO PROFIL (siswa & admin) =====
Route::middleware('auth')->group(function () {
    Route::post('/akun/foto', [FotoProfilController::class, 'update'])->name('akun.foto');
    Route::delete('/akun/foto', [FotoProfilController::class, 'destroy'])->name('akun.foto.hapus');
});

// ===== SISWA =====
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');

    Route::post('/notifikasi/baca', [SiswaDashboardController::class, 'bacaNotifikasi'])->name('notifikasi.baca');

    Route::get('/aduan/create', [SiswaAduanController::class, 'create'])->name('aduan.create');
    Route::get('/aduan/cek-ganda', [SiswaAduanController::class, 'cekGanda'])->name('aduan.cek-ganda');
    Route::post('/aduan', [SiswaAduanController::class, 'store'])->name('aduan.store');
    Route::get('/aduan/{aduan}', [SiswaAduanController::class, 'show'])->name('aduan.show');
    Route::get('/aduan/{aduan}/bukti', [SiswaAduanController::class, 'bukti'])->name('aduan.bukti');
    Route::post('/aduan/{aduan}/nilai', [SiswaAduanController::class, 'nilai'])->name('aduan.nilai');
    Route::delete('/aduan/{aduan}', [SiswaAduanController::class, 'batalkan'])->name('aduan.batalkan');

    Route::get('/riwayat', [SiswaAduanController::class, 'riwayat'])->name('riwayat');

    Route::get('/akun', [AkunController::class, 'edit'])->name('akun');
    Route::put('/akun', [AkunController::class, 'update'])->name('akun.update');
});

// ===== ADMIN / PETUGAS =====
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/data-aduan', [AdminAduanController::class, 'index'])->name('aduan.index');
    Route::get('/data-aduan/export', [AdminAduanController::class, 'export'])->name('aduan.export');
    Route::get('/data-aduan/{aduan}', [AdminAduanController::class, 'show'])->name('aduan.show');
    Route::post('/data-aduan/{aduan}/teknisi', [AdminAduanController::class, 'tugaskan'])->name('aduan.teknisi');
    Route::post('/data-aduan/{aduan}/setujui', [AdminAduanController::class, 'setujui'])->name('aduan.setujui');
    Route::post('/data-aduan/{aduan}/tolak', [AdminAduanController::class, 'tolak'])->name('aduan.tolak');
    Route::post('/data-aduan/{aduan}/selesai', [AdminAduanController::class, 'selesai'])->name('aduan.selesai');
    Route::delete('/data-aduan/{aduan}', [AdminAduanController::class, 'destroy'])->name('aduan.destroy');

    Route::get('/master-data', [MasterDataController::class, 'index'])->name('master-data');
    Route::post('/master-data/kategori', [MasterDataController::class, 'storeKategori'])->name('kategori.store');
    Route::put('/master-data/kategori/{kategori}', [MasterDataController::class, 'updateKategori'])->name('kategori.update');
    Route::delete('/master-data/kategori/{kategori}', [MasterDataController::class, 'destroyKategori'])->name('kategori.destroy');
    Route::post('/master-data/teknisi', [MasterDataController::class, 'storeTeknisi'])->name('teknisi.store');
    Route::put('/master-data/teknisi/{teknisi}', [MasterDataController::class, 'updateTeknisi'])->name('teknisi.update');
    Route::delete('/master-data/teknisi/{teknisi}', [MasterDataController::class, 'destroyTeknisi'])->name('teknisi.destroy');
    Route::post('/master-data/lokasi', [MasterDataController::class, 'storeLokasi'])->name('lokasi.store');
    Route::put('/master-data/lokasi/{lokasi}', [MasterDataController::class, 'updateLokasi'])->name('lokasi.update');
    Route::delete('/master-data/lokasi/{lokasi}', [MasterDataController::class, 'destroyLokasi'])->name('lokasi.destroy');

    Route::get('/akun', [AdminAkunController::class, 'edit'])->name('akun');
    Route::put('/akun', [AdminAkunController::class, 'update'])->name('akun.update');
    Route::put('/akun/password', [AdminAkunController::class, 'password'])->name('akun.password');
});