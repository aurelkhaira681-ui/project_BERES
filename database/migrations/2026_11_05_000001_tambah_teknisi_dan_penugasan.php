<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel teknisi (data saja, tanpa login)
        Schema::create('teknisi', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jurusan');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('teknisi')->insert(collect([
            ['Pak Indri',   'Otomotif'],
            ['Pak Joko',    'Otomasi'],
            ['Pak Sarpono', 'Mesin'],
            ['Pak Riki',    'Rekayasa Perangkat Lunak'],
            ['Pak Fajar',   'Rekayasa Perangkat Lunak'],
            ['Pak Tanto',   'Desain Pemodelan Infrastruktur Bangunan'],
        ])->map(fn ($t) => [
            'nama' => $t[0], 'jurusan' => $t[1], 'aktif' => true, 'created_at' => $now, 'updated_at' => $now,
        ])->all());

        // 2. Lokasi punya jurusan (kosong = lokasi umum, ditentukan petugas)
        Schema::table('lokasi', function (Blueprint $table) {
            $table->string('jurusan')->nullable()->after('lantai');
        });

        // 3. Aduan punya teknisi penanggung jawab
        Schema::table('aduan', function (Blueprint $table) {
            $table->unsignedBigInteger('teknisi_id')->nullable()->index()->after('lokasi_id');
        });

        // Tebak jurusan lokasi yang sudah ada dari namanya (bisa diubah admin di Master Data)
        $peta = [
            'Rekayasa Perangkat Lunak' => ['rpl', 'perangkat lunak', 'komputer'],
            'Otomotif' => ['otomotif'],
            'Otomasi' => ['otomasi'],
            'Mesin' => ['mesin', 'pemesinan'],
            'Desain Pemodelan Infrastruktur Bangunan' => ['dpib', 'gambar', 'bangunan'],
        ];
        foreach (DB::table('lokasi')->get() as $lokasi) {
            $nama = mb_strtolower($lokasi->nama_ruangan);
            foreach ($peta as $jurusan => $kata) {
                foreach ($kata as $k) {
                    if (str_contains($nama, $k)) {
                        DB::table('lokasi')->where('id', $lokasi->id)->update(['jurusan' => $jurusan]);
                        continue 3;
                    }
                }
            }
        }

        // Aduan lama yang belum selesai: tugaskan otomatis (beban paling ringan)
        $beban = [];
        $belum = DB::table('aduan')->whereIn('status', ['pending', 'approved'])->whereNull('teknisi_id')->orderBy('id')->get();
        foreach ($belum as $aduan) {
            $jurusan = DB::table('lokasi')->where('id', $aduan->lokasi_id)->value('jurusan');
            if (! $jurusan) {
                continue;
            }
            $calon = DB::table('teknisi')->where('aktif', true)->where('jurusan', $jurusan)->get();
            if ($calon->isEmpty()) {
                continue;
            }
            $pilih = $calon->sortBy(fn ($t) => $beban[$t->id] ?? 0)->first();
            DB::table('aduan')->where('id', $aduan->id)->update(['teknisi_id' => $pilih->id]);
            $beban[$pilih->id] = ($beban[$pilih->id] ?? 0) + 1;
        }
    }

    public function down(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            $table->dropColumn('teknisi_id');
        });
        Schema::table('lokasi', function (Blueprint $table) {
            $table->dropColumn('jurusan');
        });
        Schema::dropIfExists('teknisi');
    }
};