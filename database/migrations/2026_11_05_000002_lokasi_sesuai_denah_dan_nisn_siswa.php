<?php

use Database\Seeders\LokasiDenahSeeder;
use Database\Seeders\SiswaTerdaftarSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Lokasi: kode (penghubung ke denah klik) + kelompok (pengelompokan di daftar teks)
        Schema::table('lokasi', function (Blueprint $table) {
            $table->string('kode')->nullable()->unique()->after('id');
            $table->string('kelompok')->nullable()->after('lantai');
        });

        // 2. Daftar NISN siswa yang boleh mendaftar / masuk (dari data Peserta Didik Kelas XII)
        Schema::create('siswa_terdaftar', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 10)->unique();
            $table->string('nama');
            $table->string('kelas');
            $table->string('nipd')->nullable();
            $table->timestamps();
        });

        (new LokasiDenahSeeder)->run();
        (new SiswaTerdaftarSeeder)->run();
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_terdaftar');

        Schema::table('lokasi', function (Blueprint $table) {
            $table->dropUnique(['kode']);
            $table->dropColumn(['kode', 'kelompok']);
        });
    }
};