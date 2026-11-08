<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use Illuminate\Database\Seeder;

// Mengisi tabel lokasi dari denah sekolah (config/denah.php).
// Jurusan/bidang tiap ruangan diisi otomatis dari namanya (lihat Lokasi::ATURAN_JURUSAN),
// supaya aduan langsung diarahkan ke teknisi yang tepat. Ruangan sisa dibiarkan kosong -> Pak Jiwanto.
// Aman dijalankan berulang: ruangan yang sudah ada tidak diduplikasi, dan jurusan yang sudah
// diatur manual di Master Data tidak ditimpa.
// Jalankan sendiri dengan: php artisan db:seed --class=LokasiDenahSeeder
class LokasiDenahSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('denah.ruangan', []) as $ruangan) {
            $lokasi = Lokasi::firstOrCreate(
                ['nama_ruangan' => $ruangan['nama']],
                ['lantai' => $ruangan['lantai']],
            );

            if (blank($lokasi->jurusan)) {
                $jurusan = Lokasi::jurusanDariNama($lokasi->nama_ruangan);

                if ($jurusan) {
                    $lokasi->update(['jurusan' => $jurusan]);
                }
            }
        }
    }
}
