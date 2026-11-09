<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            // Bukti hasil perbaikan: foto sesudah + catatan singkat dari petugas
            $table->json('foto_selesai')->nullable()->after('foto');
            $table->text('catatan_perbaikan')->nullable()->after('catatan_admin');
        });
    }

    public function down(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            $table->dropColumn(['foto_selesai', 'catatan_perbaikan']);
        });
    }
};