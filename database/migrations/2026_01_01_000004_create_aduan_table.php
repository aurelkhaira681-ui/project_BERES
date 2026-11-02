<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aduan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tiket')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kategori_kerusakan_id')->constrained('kategori_kerusakan');
            $table->foreignId('lokasi_id')->constrained('lokasi');
            $table->dateTime('tanggal_kejadian');
            $table->text('deskripsi');
            $table->json('foto')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'selesai'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamp('diverifikasi_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aduan');
    }
};
