<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Memuat daftar NISN siswa dari database/data/siswa_kelas_xii.csv (kolom: nisn,nama,kelas,nipd).
 * NISN selalu 10 digit (angka 0 di depan dipulihkan). Aman dijalankan berulang (upsert).
 *
 * Tahun ajaran baru: ganti isi CSV lalu jalankan
 *   php artisan db:seed --class=SiswaTerdaftarSeeder
 */
class SiswaTerdaftarSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('data/siswa_kelas_xii.csv');
        if (! is_file($file)) {
            return;
        }

        $fh = fopen($file, 'r');
        $head = fgetcsv($fh);
        $now = now();
        $baris = [];

        while (($row = fgetcsv($fh)) !== false) {
            $r = array_combine($head, $row);
            $nisn = str_pad(preg_replace('/\D/', '', $r['nisn'] ?? ''), 10, '0', STR_PAD_LEFT);
            if ($nisn === '0000000000' || blank($r['nama'] ?? null)) {
                continue;
            }
            $baris[] = [
                'nisn' => $nisn,
                'nama' => trim($r['nama']),
                'kelas' => trim($r['kelas']),
                'nipd' => $r['nipd'] ?: null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        fclose($fh);

        foreach (array_chunk($baris, 200) as $potong) {
            DB::table('siswa_terdaftar')->upsert($potong, ['nisn'], ['nama', 'kelas', 'nipd', 'updated_at']);
        }
    }
}