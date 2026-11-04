<?php

namespace Database\Seeders;

use App\Models\Aduan;
use App\Models\KategoriKerusakan;
use App\Models\Lokasi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin/Petugas
        $admin = User::create([
            'role' => 'admin',
            'name' => 'Aurel Khaira Lubna',
            'email' => 'admin@smkn5solo.sch.id',
            'password' => Hash::make('password'),
        ]);

        // Akun Siswa contoh
        $siswa = User::create([
            'role' => 'siswa',
            'name' => 'Aurel Khaira Lubna',
            'email' => 'aurel.khaira@siswa.sch.id',
            'password' => Hash::make('password'),
            'nisn' => '0071234589',
            'kelas' => 'XII RPL B',
            'no_presensi' => '11',
            'konsentrasi_keahlian' => 'Rekayasa Perangkat Lunak',
            'no_hp' => '0812-3456-7890',
        ]);

        $kategori = collect([
            ['nama' => 'Elektronik', 'icon' => '💡'],
            ['nama' => 'Sanitasi', 'icon' => '🚿'],
            ['nama' => 'Furnitur', 'icon' => '🪑'],
            ['nama' => 'Lainnya', 'icon' => '🧩'],
        ])->map(fn ($item) => KategoriKerusakan::create($item));

        // Daftar ruangan diambil dari denah sekolah (config/denah.php)
        $this->call(LokasiDenahSeeder::class);

        $lokasi = collect(['Lab. RPL 1', 'WC (dekat Lab. RPL 4)', 'Rg 1.07'])
            ->map(fn ($nama) => Lokasi::where('nama_ruangan', $nama)->firstOrFail());

        Aduan::create([
            'user_id' => $siswa->id,
            'kategori_kerusakan_id' => $kategori[0]->id,
            'lokasi_id' => $lokasi[0]->id,
            'tanggal_kejadian' => now()->subDays(2),
            'deskripsi' => 'AC di sisi kanan ruangan mengeluarkan suara kasar dan tidak mengeluarkan udara dingin sejak Senin pagi.',
            'status' => 'pending',
        ]);

        Aduan::create([
            'user_id' => $siswa->id,
            'kategori_kerusakan_id' => $kategori[1]->id,
            'lokasi_id' => $lokasi[1]->id,
            'tanggal_kejadian' => now()->subDays(6),
            'deskripsi' => 'Kran wastafel toilet putri bocor terus menerus.',
            'status' => 'approved',
            'diverifikasi_at' => now()->subDays(5),
        ]);

        Aduan::create([
            'user_id' => $siswa->id,
            'kategori_kerusakan_id' => $kategori[0]->id,
            'lokasi_id' => $lokasi[2]->id,
            'tanggal_kejadian' => now()->subDays(12),
            'deskripsi' => 'Proyektor kelas XII RPL B redup dan sulit terbaca.',
            'status' => 'rejected',
            'catatan_admin' => 'Sudah dicek, proyektor masih normal. Silakan cek pengaturan kecerahan terlebih dahulu.',
            'diverifikasi_at' => now()->subDays(10),
        ]);
    }
}
