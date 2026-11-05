<?php

use Database\Seeders\LokasiDenahSeeder;
use Database\Seeders\SiswaTerdaftarSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Lokasi: kode (penghubung ke denah klik) + kelompok (pengelompokan di daftar teks)
        //    Setiap kolom dicek dulu supaya aman dijalankan pada database yang sudah memilikinya.
        if (! Schema::hasColumn('lokasi', 'kode')) {
            Schema::table('lokasi', function (Blueprint $table) {
                $table->string('kode')->nullable()->unique()->after('id');
            });
        }

        if (! Schema::hasColumn('lokasi', 'kelompok')) {
            Schema::table('lokasi', function (Blueprint $table) {
                $table->string('kelompok')->nullable()->after('lantai');
            });
        }

        // 2. Daftar NISN siswa yang boleh mendaftar / masuk (dari data Peserta Didik Kelas XII)
        if (! Schema::hasTable('siswa_terdaftar')) {
            Schema::create('siswa_terdaftar', function (Blueprint $table) {
                $table->id();
                $table->string('nisn', 10)->unique();
                $table->string('nama');
                $table->string('kelas');
                $table->string('nipd')->nullable();
                $table->timestamps();
            });
        }

        // Seeder lokasi aman diulang (firstOrCreate).
        (new LokasiDenahSeeder)->run();

        // Seeder siswa hanya dijalankan jika tabel masih kosong, agar tidak terjadi NISN ganda.
        if (DB::table('siswa_terdaftar')->count() === 0) {
            (new SiswaTerdaftarSeeder)->run();
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_terdaftar');

        if (Schema::hasColumn('lokasi', 'kode')) {
            Schema::table('lokasi', function (Blueprint $table) {
                $table->dropUnique(['kode']);
            });
        }

        $hapus = array_values(array_filter(
            ['kode', 'kelompok'],
            fn ($kolom) => Schema::hasColumn('lokasi', $kolom)
        ));

        if ($hapus) {
            Schema::table('lokasi', function (Blueprint $table) use ($hapus) {
                $table->dropColumn($hapus);
            });
        }
    }
};