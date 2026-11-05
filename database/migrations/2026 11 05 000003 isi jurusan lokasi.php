<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Mengisi jurusan lokasi yang masih kosong berdasarkan namanya, supaya penugasan
// teknisi mengikuti jurusan lokasi (bukan jatuh semua ke Pak Jiwanto).
// Hanya mengisi yang NULL, jadi pengaturan manual di Master Data tidak tertimpa.
// Ruangan Listrik -> Pak Purdiyanto; ruang kelas biasa, kantor, WC, dan fasilitas umum sengaja
// dibiarkan kosong (= ruangan sisa -> Pak Jiwanto).
return new class extends Migration
{
    private const PETA = [
        'Listrik' => ['listr', 'dle'],
        'Elektro' => ['elektro', 'elektronika'],
        'Otomotif' => ['tsm', 'tkr', 'otomotif'],
        'Mesin' => ['pemesinan'],
        'Desain Pemodelan Infrastruktur Bangunan' => ['dpib'],
        'Rekayasa Perangkat Lunak' => ['axioo', 'rpl', 'kkpi', 'kkp'],
        'Otomasi' => ['otomasi'],
    ];

    public function up(): void
    {
        // Ruangan Listrik yang sempat terisi 'Elektro' dikoreksi menjadi 'Listrik'.
        DB::table('lokasi')->where('jurusan', 'Elektro')->where('nama_ruangan', 'like', '%listr%')->update(['jurusan' => 'Listrik']);

        foreach (DB::table('lokasi')->whereNull('jurusan')->get() as $lokasi) {
            $nama = mb_strtolower($lokasi->nama_ruangan);

            foreach (self::PETA as $jurusan => $kataKunci) {
                foreach ($kataKunci as $kata) {
                    if (str_contains($nama, $kata)) {
                        DB::table('lokasi')->where('id', $lokasi->id)->update(['jurusan' => $jurusan]);
                        continue 3;
                    }
                }
            }
        }
    }

    public function down(): void
    {
        // Tidak ada yang perlu dikembalikan.
    }
};