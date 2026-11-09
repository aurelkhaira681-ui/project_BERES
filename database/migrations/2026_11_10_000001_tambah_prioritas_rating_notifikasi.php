<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            // Prioritas: rendah | sedang | mendesak
            $table->string('prioritas', 10)->default('sedang')->index()->after('status');

            // Rating hasil perbaikan (1-5) + ulasan singkat dari siswa
            $table->unsignedTinyInteger('rating')->nullable()->after('catatan_perbaikan');
            $table->text('ulasan')->nullable()->after('rating');
            $table->timestamp('dinilai_at')->nullable()->after('ulasan');
        });

        Schema::table('users', function (Blueprint $table) {
            // Penanda lonceng notifikasi: pembaruan aduan setelah waktu ini dianggap belum dibaca
            $table->timestamp('notif_dibaca_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            $table->dropColumn(['prioritas', 'rating', 'ulasan', 'dinilai_at']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('notif_dibaca_at');
        });
    }
};