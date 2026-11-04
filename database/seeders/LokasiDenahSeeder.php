<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use Illuminate\Database\Seeder;

// Mengisi tabel lokasi dari denah sekolah (config/denah.php).
// Aman dijalankan berulang: ruangan yang sudah ada tidak diduplikasi.
// Jalankan sendiri dengan: php artisan db:seed --class=LokasiDenahSeeder
class LokasiDenahSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('denah.ruangan', []) as $ruangan) {
            Lokasi::firstOrCreate(
                ['nama_ruangan' => $ruangan['nama']],
                ['lantai' => $ruangan['lantai']],
            );
        }
    }
}
